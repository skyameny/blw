<?php
/**
 * 用户管理异常
 */

namespace authority\exception;

use core\exception\CoreException;

class UserException extends CoreException
{
    protected $status_code = STATUS_CODE_USER_FAILED;
    
}