<?php
/**
 * 异常类
* @author keepwin100
* @package emicall
* @copyright 苏ICP备08006818号
*/
namespace core\exception;

use Exception;

class ServiceException extends CommonException
{
    protected  $sign = "service";

    public function __construct($message = null, $code = SYSTEM_ERROR, Exception $previous = null)
    {
        parent::__construct($message, $code);
    }
     
    public function toString($message)
    {
        return get_class($this).$message;
    }
}