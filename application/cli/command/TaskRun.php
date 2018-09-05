<?php
/**
 * 清除通话记录 定时器
 * 主要完成 清除通话记录
 * 
 * #1个月执行一次 清理一个月的通话记录 每月凌晨1点执行
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
use moudles\inspection\services\impl\ProcessService;

/**
 * Description of exportCsv
 *
 * @author keepwin100
 */
class TaskRun extends Command
{
    protected  function configure()
    {
        $this->setName('taskrun')->setDescription('run the worker do task!');
        //设置第一参数 清理范围
        $this->addArgument('eid');//参数  
    }
    /**
     * 执行命令 php think clearup
     * @see \think\console\Command::execute()
     */
    public function execute(Input $input, Output $output){
        ExLog::log("启动后台程序...",ExLog::DEBUG);
        if($input->getArgument("eid")){
           helpersEnterprise::setEnvironment($input->getArgument("eid"));
        }
        //require_once '/var/pbx/website/vendor/chrisboulton/php-resque/extras/sample-plugin.php';
        if(!defined("WORKER_ORGINAL_MEMORY")) define("WORKER_ORGINAL_MEMORY", memory_get_usage());
        ProcessService::startService();
        $output->write("初始化完成\n");
    }
}