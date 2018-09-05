<?php
//0 1 */1 * *  php /var/pbx/website/think service {$option} {$count}
/**
 *
 * @author Dream<hukaijun@emicnet.com>
 * 
 */

namespace app\cli\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use moudles\core\utils\ExLog;
use think\Config;
use think\console\input\Argument;
use moudles\inspection\services\impl\ProcessService;

/**
 * Description of Service
 *  php /var/pbx/website/think service  start   1
 * @author keepwin100
 * php /var/pbx/
 */
class Service extends Command
{
    const LOG_KEEP_DAYS = 7;
    
    protected  function configure()
    {
        config::set("AUTHOR","contab");
        
        $this->setName('service')->setDescription('service to manage  worker process!');
        //设置第一参数 清理范围
        $this->addArgument('o',Argument::REQUIRED);//参数 允许 record 和log 默认都清理
        //设置第二参数 清理企业ID
        $this->addArgument('p');//参数
    }
    /**
     * 执行命令 php think clearup
     * @see \think\console\Command::execute()
     */
    public function execute(Input $input, Output $output){
        $option = $input->getArgument("o");
        if(empty($option)){
            $output->warning("Params number error!");
        }
        switch ($option) {
            case "start":
                $process_num = $input->getArgument('p');
                if($process_num < 0 ){
                    $ret = false;
                    $output->write($ret);
                }
                $workers =  ProcessService::getWorkers();
                $count = count($workers);
                if($count > 0){
                    $process_num -= $count;
                }
                if($process_num < 0){
                    $times = abs($process_num);
                    foreach ($workers as $k => $worker) {
                        if($k < $times){
                            list ($hostname, $pid, $queues) = explode(':', (string) $worker, 3);
                            posix_kill($pid, 3);
                            ExLog::log("stop pid[" . $pid . "] ok!");
                        }
                    }
                }else {
                    ProcessService::websiteStart($process_num);
                }
                break;
            case "restart":
                ProcessService::stop();
                ProcessService::websiteStart();
                break;
            case "stop":
                ProcessService::stop();
                break;
            case "list":
                $workers =  ProcessService::getWorkers();
                $output->write($workers);
                $output->write(PHP_EOL);
                break;
                exit;
        }
        
        ExLog::log("正在【".$option."】服务...");
        $output->write("success!".PHP_EOL);
    }
    
    
    
}