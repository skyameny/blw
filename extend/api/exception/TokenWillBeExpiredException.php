<?php
namespace  api\exception;


class TokenWillBeExpiredException extends ApiException
{
    protected $errorcode = STATUS_API_TOKEN_WILL_BE_EXPIRED;
}