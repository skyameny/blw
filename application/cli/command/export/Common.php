<?php
/**
 * 全局公共导出类
 * 不依赖任何业务
 * @date: 2018年10月18日 下午5:00:02
 * @author: Dream<hukaijun>
 */
namespace app\cli\command\export;

use core\exception\CoreException;
use core\utils\ExLog;
use core\utils\export\EmExport;
use think\console\Command;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;
use \ReflectionClass;

final class Common extends Command
{
    protected function configure()
    {
        $this->setName('export:common')->setDescription('Export common command!');
        $this->addOption('params', "p", Option::VALUE_REQUIRED);
        $this->addOption('hash', "f", Option::VALUE_REQUIRED);
    }

    public function execute(Input $input, Output $output)
    {
        $params = $this->checkParams($input->getOption("params"));
        $hash = $input->getOption("hash");
        $export_class = $params["class_name"];
        if(class_exists($export_class)){
            $object = new ReflectionClass($export_class);
            $method = $object->getmethod('getInstance');
            $instance = null;
            if($method->isStatic() && $method->isPublic()){
                $instance = $method->invokeArgs(null,
                    [$hash,$params]);
            }
            if(!$instance instanceof EmExport){
                ExLog::log("导出失败：无法创建对象[$export_class]",ExLog::DEBUG);
                exit("导出失败");
            }
            if($instance->getStatus() != EmExport::STATUS_RUNNING){
                ExLog::log("导出失败：该任务状态为[".EmExport::STATUS_RUNNING."]",ExLog::DEBUG);
                exit("导出失败");
            }
            //开始运行
            $instance->process();
            ExLog::log("导出完成:".$instance->getFile());
        }else {
            ExLog::log("导出失败：参数有误[" . $input->getOption("params") . "]", ExLog::DEBUG);
            exit("参数有误");
        }
    }

    /**
     * 检查参数列表
     * @param $up
     * @return array|mixed
     * @throws CoreException
     */
    private function checkParams($up)
    {
        $safe_params = [];
        $params = json_decode($up,true);
        if(empty($params)){
            throw new CoreException(STATUS_CODE_PARAM_ERROR);
        }
        if(!isset($params["eid"]) || !is_numeric($params["eid"])){
            throw new CoreException(STATUS_CODE_PARAM_ERROR,"缺少EID");
        }
        $safe_params = $params;
        return $safe_params;
    }

}