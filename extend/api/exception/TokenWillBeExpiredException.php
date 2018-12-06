<?php
namespace  api\exception;


class TokenWillBeExpiredException extends ApiException
{
    protected $status_code = STATUS_API_TOKEN_WILL_BE_EXPIRED;
}