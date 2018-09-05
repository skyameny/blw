<?php
/**
 * 服务器配置cli
 * 
 * @author Dream<hukaijun@emicnet.com> 
 * @update zhangmengfan<zhangmengfan@emicnet.com>
 */
namespace app\cli\command;

use think\console\Command;
use think\console\input\Argument;
use think\console\Input;
use think\console\Output;
use moudles\inspection\includes\que_task\Monitor;
use moudles\core\models\SystemConfig;

/**
 * Description of creat
 * php /var/pbx/website/think Config {$option} {$eid} {$key} {$value} {$describe} {$type}
 */
class Config extends Command
{

    // 监控器
    private $monitor = null;

    protected function configure()
    {
        $this->setName('config')->setDescription('You can get & set System config,  Oh Oh Oh  and  delete!');
        // 设置参数
        
        $this->addArgument('option', Argument::REQUIRED); // 参数 选项 set get del
        $this->addArgument('eid'); // 参数 企业id
        $this->addArgument('key'); // 参数 key
        $this->addArgument('value'); // 参数 value
        $this->addArgument('describe'); // 参数 描述
    }

    /**
     * 执行命令 php think clearup
     * @see \think\console\Command::execute()
     */
    public function execute(Input $input, Output $output)
    {
        if(get_current_user() !== "www-data"){
            $output->write("Please use www-data!".PHP_EOL);
            exit();
        }
        $option = $input->getArgument("option");
        $key = $input->getArgument("key");
        $value = $input->getArgument("value");
        $eid = intval($input->getArgument("eid"));
        $describe = $input->getArgument("describe");

        switch ($option) {
            case "get":
                $ret = SystemConfig::getValue($key, $eid, true);
                if (empty($ret)) {
                    $output->write(-1);
                } elseif (empty($key) && is_array($ret)) {
                    foreach ($ret as $v) {
                        $output->write($v['key'] . ' : ' . $v['value'] . PHP_EOL);
                    }
                } else {
                    $output->write($ret['key'] . ': ' . $ret['value'] . PHP_EOL);
                }
                break;
            case "set":
                if (! empty($key) && ! empty($value)) {
                    $ret = SystemConfig::getValue($key, $eid, true);
                    if (empty($ret) || $ret['type'] != 0) {
                        SystemConfig::setValue($key, $value, $eid, $describe);
                        $output->write(0);
                        exit();
                    } else {
                        $output->write(-1);
                    }
                } else {
                    $output->write(-1);
                }
                break;
            case "del":
                if (empty($key)) {
                    $output->write(-1);
                    exit();
                }
                $ret = SystemConfig::getValue($key, $eid, true);
                if ($ret['type'] == 2) {
                    SystemConfig::destroy([
                        'ep_id' => $eid,
                        'key' => $key
                    ]);
                    $output->write(0);
                } else {
                    $output->write(-1);
                }
                break;
            default:
                $output->write(-1);
                break;
        }
    }
}