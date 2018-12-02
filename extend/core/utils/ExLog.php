<?php
/**
 * @author Keepwin100
 * @from Talksystem 
 */
namespace core\utils;

use think\Log;
use think\Config;

class ExLog extends Log {
	/**
	 * 只记录允许记录的日志级别，日志为对象或数组的转换为文本
	 * @param string|object $message 日志信息
	 * @param string $level 日志级别
	 */
	public static function log($message,$level=self::ERROR){
	 	if(in_array($level,Config::get('log_level'))) {
	 	   if(is_array($message) || is_object($message)) $message = print_r($message,true);
	 	   if(config("app_debug")){
	 	       $message = '<'.Config::get("AUTHOR").'>'.$message;
	 	   }
           self::write($message,$level);
        }
	}
}