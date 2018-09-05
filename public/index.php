<?php
namespace think;
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]
error_reporting(E_ALL);
// register_shutdown_function(function(){ var_dump(error_get_last()); });
// 定义应用目录
define('APP_PATH', __DIR__ . '/../application/');
define("BLW_LOG_PATH", "/var/www/tmp/");
define("EXTEND_PATH", APP_PATH."../extend/");

// 加载框架引导文件
//require __DIR__ . '/../thinkphp/base.php';
// 执行应用并响应
//Container::get('app')->run()->send();
require __DIR__ . '/../thinkphp/start.php'; 
