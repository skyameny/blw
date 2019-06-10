<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------

$apps =  array_map('basename', glob(APP_PATH . '*', GLOB_ONLYDIR));

$returnCommands = [
    'app\cli\command\SmsMessage',
    'app\cli\command\ClearUp',
    'app\cli\command\Enterprise',
    'app\cli\command\TaskRun',
    'app\cli\command\NlpWork',
    'app\cli\command\Config',
    'app\cli\command\Service',
    'app\cli\command\Kafka'
];
foreach ($apps as $app) {
    $commandFile = APP_PATH . $app . '/command.php';
    
    if (file_exists($commandFile)) {
        $commands       = include $commandFile;
        
        $returnCommands = array_merge($returnCommands, $commands);
    }
}


return $returnCommands;
