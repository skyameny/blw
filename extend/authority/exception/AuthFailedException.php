<?php
/**
 * 登录失败异常类
 * @author keepwin100
 * @package emicall
 * @copyright 苏ICP备08006818号
 */
namespace authority\exception;
use core\exception\CoreException;

class AuthFailedException extends CoreException {

    protected $status_code = STATUS_CODE_AUTH_FAILED;

    public function __toString()
    {
        return get_class($this) . " '{$this->message}' in {$this->file}({$this->line})\n
        {$this->getTraceAsString()}";
    }

}