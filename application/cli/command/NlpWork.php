<?php
/**
 * NLP质检cli
 * 
 * @author Dream<hukaijun@emicnet.com>
 */

namespace app\cli\command;

use think\console\Command;
use think\console\input\Argument;
use think\console\Input;
use think\console\Output;
use moudles\core\utils\ExLog;
use moudles\inspection\utils\helper\helpersEnterprise;
use moudles\inspection\models\Task;
use moudles\inspection\includes\que_task\Monitor;
use moudles\inspection\includes\nlp\NlpWorker;

/**
 * Description of creat
 * php /var/pbx/website/think nlp 5 QIS201807041548543200261631
 *
 */
class NlpWork extends Command
{
    //监控器
    private $monitor = null;
    
    protected  function configure()
    {
        $this->setName('nlp')->setDescription('Nlp service work to task!');
        //设置参数
        //option option='delete' 'create'
        $this->addArgument('eid',Argument::REQUIRED);//参数
        $this->addArgument('tnumber',Argument::REQUIRED);//参数 任务订单号
        //这个eid是指qis_enterprise主键id
        //$this->addArgument('');//参数
    }

    /**
     * 执行命令 php think clearup
     *
     * @see \think\console\Command::execute() 
     */
    public function execute(Input $input, Output $output)
    {
        $eid = $input->getArgument("eid");
        if ($eid) {
            helpersEnterprise::setEnvironment($eid);
        }
        $task_number = $input->getArgument("tnumber");
        $task = Task::get([
            "number" => $task_number
        ]);
        if (! $task) {
            ExLog::log("不存在该任务！", ExLog::DEBUG);
            $output->write("no task cli stop！");
            exit(0);
        }
        if (!Monitor::isRuning($task) || ($task->getAttr("state") != Task::TASK_STATUS_RUNNING)) {
            ExLog::log("该任务不在监控中！", ExLog::DEBUG);
            $output->write("cli stop！");
            exit(0);
        }
        $nlp = new NlpWorker($task); // 质检NLP服务
        $nlp->work();
        ExLog::log("NLP质检线程结束...");
        $output->write("success");
    }
}