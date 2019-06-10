<?php
namespace authority\exception;


use core\exception\CoreException;

class PasswordException extends CoreException
{
    protected $errorcode = STATUS_CODE_NONSTANDARD_PASSWORD;
}