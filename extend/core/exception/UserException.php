<?php
/**
 * 用户管理异常
 */

namespace core\exception;


class UserException extends CommonException
{
    protected $status_code = STATUS_CODE_LOGIN_EXITS;
    
}