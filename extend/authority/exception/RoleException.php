<?php 
/**
 * 角色异常
 */
namespace authority\exception;

use core\exception\CoreException;

class RoleException extends CoreException
{
    protected $errorcode = STATUS_CODE_ROLE_FAILED;


}


?>