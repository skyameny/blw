<?php
/**
 * common异常类
 * @author keepwin100
 * @package core
 * @copyright 苏ICP备08006818号
 */
namespace core\exception;

use Exception;
use core\utils\ExLog;

class CommonException extends Exception
{
    protected $status_code = STATUS_CODE_SYSTEM_ERROR;

    public function __construct($message = null, $code = null, Exception $previous = null)
    {
        $this->code = empty($code) ? $this->status_code : $code; 
        $message = empty($message) ? config(ERROR_PREFIX . $code) : $message;
        parent::__construct($message, $this->code);
//         if ($this->sign) {
        ExLog::log("[" . $this->status_code . "]" . $message, ExLog::DEBUG);
//         }
    }

    public function __toString()
    {
        return get_class($this) . " '{$this->message}' in {$this->file}({$this->line})\n
        {$this->getTraceAsString()}";
    }
}