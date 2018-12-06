<?php
namespace  api\exception;

use core\exception\CommonException;

class ApiException extends CommonException
{
    protected  $status_code = STATUS_API_EXCEPTION;
}