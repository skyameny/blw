<?php
/**
 * 企业管理器
 * 主要完成企业的管理
 * @author Dream<hukaijun@emicnet.com>
 */

namespace app\cli\command;

use think\console\Command;
use think\console\input\Argument;
use think\console\Input;
use think\console\Output;
use think\Db;
use moudles\core\utils\ExLog;
use moudles\inspection\utils\helper\helpersEnterprise;

/**
 * Description of creat
 *
 * 
 */
class Enterprise extends Command
{
    protected  function configure()
    {
        $this->setName('epm')->setDescription('Enterprise management tools!');
        //设置参数
        //option option='delete' 'create' 
        $this->addArgument('option',Argument::REQUIRED);//参数
        //这个eid是指qis_enterprise主键id
        $this->addArgument('eid');//参数  

    }

    /**
     * 执行命令 php think clearup
     * 
     * @see \think\console\Command::execute()
     */
    public function execute(Input $input, Output $output)
    {
        $eid = $input->getArgument("eid");
        // 删除企业
        if ($input->getArgument("option") == "delete") {
            $seid = helpersEnterprise::formatEpDbName($eid);
            $db_name = ENTERPRISE_DB_PREFIX."_" . $seid;
            //删除企业
            Db::execute("DROP DATABASE  `" . $db_name . "`");
            ExLog::log("正在删除企业[" . $eid . "]", ExLog::DEBUG);
        } // 创建企业
        elseif ($input->getArgument("option") == "create") {
            $seid = helpersEnterprise::formatEpDbName($eid);
            $db_name = ENTERPRISE_DB_PREFIX."_" . $seid;
            ExLog::log("正在创建企业[" . $eid . "]", ExLog::DEBUG);
            $res2 = Db::execute("show databases like '".$db_name."'");
            if ($res2==1) {
                $output->warning("该企业已经存在");
                ExLog::log("无法创建企业" . $eid);
                exit();
            }
            Db::startTrans();//启动事务
            ExLog::log("正在准备创建企业库[" . $db_name . "]...", ExLog::INFO);
            // 创建数据库
            try {
                $result = Db::execute("CREATE DATABASE  `" . $db_name . "`  DEFAULT  CHARACTER SET  utf8 COLLATE  utf8_general_ci");
                ExLog::log("创建企业表[" . $db_name . "]开始...");
                $this->resource($eid);
                Db::commit();
            } catch (\Exception $e) {
                Db::rollback();
                ExLog::log("创建企业库[" . $db_name . "]失败:".$e->getMessage());
                //删除企业
                Db::execute("DROP DATABASE  `" . $db_name . "`");
                $output->write("failed!");
                exit;
            }
            //创建目录
            $output->write("success");
        }
    }
    /**
     * 创建数据表
     */
    public function resource($eid)
    {
        $db = helpersEnterprise::getEnterpriseDb($eid); 
       // $db=Db::connect('mysql://root:C1oudP8x&2017@127.0.0.1:3306/'.$db_name.'#utf8');
        // 创建表
        ExLog::log("正在创建企业表", ExLog::INFO);
        $sql_path = INSTALL_UPDATE_PATH."install_enterprise.sql";
        $context = stream_context_create ( array (
            'http' => array (
                'timeout' => 30
            )
        ) ) ;// 超时时间，单位为秒
        $sql = file_get_contents ( $sql_path, 0, $context );
        $sql = str_replace ( "\r", "\n", $sql );
        $sql = explode ( ";\n", $sql );
        
        // 创建数据表
        //执行sql语句
        foreach ($sql as  $_value) {
            if(empty($_value)){ 
                continue;
            }
            $db->free();
            $result = $db->execute($_value);
        }
        return true;
    }
    
}