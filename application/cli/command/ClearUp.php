<?php
//0 1 */1 * *  php /var/pbx/website/think clearup 
/**
 * 清除通话记录 定时器
 * 主要完成 清除通话记录
 * 
 *  每月凌晨1点执行
 * 
 * 
 * @author Dream<hukaijun@emicnet.com>
 */

namespace app\cli\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use moudles\core\utils\ExLog;
use moudles\inspection\utils\helper\helpersEnterprise;
use think\Db;
use moudles\inspection\models\Enterprise;
use think\Config;
use think\db\Query;

/**
 * Description of ClearUp
 *
 * @author keepwin100
 * php /var/pbx/
 */
class ClearUp extends Command
{
    const LOG_KEEP_DAYS = 7;
    
    const RECORD_KEEP_DAYS = 30;
    
    protected  function configure()
    {
        config::set("AUTHOR","contab");
        
        $this->setName('clearup')->setDescription('clearup callrecord to system!');
        //设置第一参数 清理范围
        $this->addArgument('type');//参数 允许 record 和log 默认都清理
        //设置第二参数 清理企业ID
        $this->addArgument('eid');//参数  
    }
    /**
     * 执行命令 php think clearup
     * @see \think\console\Command::execute()
     */
    public function execute(Input $input, Output $output){
        ExLog::log("正在清理系统数据...");
        $enterprises =[];
        $type = $input->getArgument("type");
        $eid  = $input->getArgument("eid");
        //log
        if($type == "log" || empty($type)){
            ExLog::log("正在清理日志文件");
            $log_path = QIS_LOG_PATH;
            $one_week_ago = time()-self::LOG_KEEP_DAYS*86400;
            $files = [];
            helpersEnterprise::getAllFiles($type, $files);
            foreach ($files as $f){
                if(fileatime($f) < $one_week_ago){
                    ExLog::log("正在清理一个星期前日志文件".$f,ExLog::DEBUG);
                    @unlink($f);
                }
            }
            $output->write("清除日志文件完成!".PHP_EOL);
        }
        if($type == "record" || empty($type)){
            $one_month_ago = time()-self::RECORD_KEEP_DAYS*86400;
            $enterprises = Enterprise::all();
            $config = Config::get('database');
            foreach ($enterprises as $enterprise)
            {
                $eid = $enterprise->getAttr("id");
                ExLog::log("开始清理企业：".$enterprise->getAttr("ep_name")."<<$eid>>",ExLog::DEBUG);
                $config["database"] ='qis_'.helpersEnterprise::formatEpDbName($eid);
                $db = Db::connect($config);
                $res = $db->name("task")->where("create_time","<",$one_month_ago)->field("id")->order("id","asc")->select();
                if(empty($res)){
                    ExLog::log("企业没有需要清理的数据",ExLog::DEBUG);
                    continue;
                }
                $res_string = implode(",", array_column($res, "id"));
                //删除任务
                $db->name("task")->where("create_time","<",$one_month_ago)->delete();
                ExLog::log("正在删除过期任务".$db->getLastSql(),ExLog::DEBUG);
                //删除结果
                $res = $db->name("result")->where("task_id","in",$res_string)->delete();
                ExLog::log("正在删除过期结果".$db->getLastSql(),ExLog::DEBUG);
                //删除质检项
                $res = $db->name("item")->where("task_id","in",$res_string)->delete();
                ExLog::log("正在删除过期质检项".$db->getLastSql(),ExLog::DEBUG);
                //删除话单
                $sql = 'select record_id,count(id) as count from qis_task_record  where record_id in(select record_id from `qis_task_record` where task_id in ('.$res_string.') ) group by record_id';
                $res = $db->name("task_record")->query($sql);
                ExLog::log("正在查询话单".$db->getLastSql(),ExLog::DEBUG);
                //删除没有其他引用的结果
                $once_records =[]; 
                if(!empty($res)){
                    foreach ($res as $rec)
                    {
                        if($rec["count"] == 1){
                            $once_records[] = $rec["record_id"];
                        }
                    }
                }
                if(!empty($once_records)){
                    sort($once_records);
                    $db->name("call_record")->delete($once_records);
                    ExLog::log("正在删除过期话单".$db->getLastSql(),ExLog::DEBUG);
                }
                //删除任务话单
                $res = $db->name("item")->where("task_id","in",$res_string)->delete();
                ExLog::log("正在删除过期话单".$db->getLastSql(),ExLog::DEBUG);
            }
        }
        $output->write("success!".PHP_EOL); 
    }


    
}