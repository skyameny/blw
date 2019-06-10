<?php
/**
 * 导出异常.
 * User: keepwin100
 * Date: 2019-03-22
 * Time: 10:06
 */
namespace core\utils\export\exception;

use core\utils\ExLog;
use think\Exception;

final class ExportException extends Exception
{
    public function __construct($message, $code, $previous = null)
    {
        $message = '导出错误:' . $message;
        parent::__construct($message, $code, $previous);
        ExLog::log("$this"); //日志
    }
    public function __toString()
    {
        return  $this->getMessage()."[".$this->code."]";
    }
}