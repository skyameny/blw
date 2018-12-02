<?php
namespace  api\exception;

use core\exception\CommonException;

class ApiException extends CommonException
{
    protected  $errorcode = STATUS_API_EXCEPTION;
}