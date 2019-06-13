<?php
/**
 * 会员异常
 * User: keepwin100
 * Date: 2019-06-12
 * Time: 14:02
 */

namespace authority\exception;

use core\exception\CoreException;

class MemberException extends CoreException
{
    protected $status_code = STATUS_CODE_MEMBER_FAILED;
}