<?php
namespace api\validate;

use core\validate\CoreValidate;

class EnterpriseValidate extends CoreValidate
{
    protected $rule = [
        'page' => 'number',
        'limit' => 'number',
    ];
    
    protected $message = [
        'page.number' => PARAM_TYPE_ERROR,
        'limit.number' => PARAM_TYPE_ERROR,
    ];
    
    protected $scene = [
        'getscripts' => ['page','limit'],
    ];
}