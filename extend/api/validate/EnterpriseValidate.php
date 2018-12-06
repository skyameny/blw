<?php
namespace api\validate;

use core\validate\CoreValidate;

class EnterpriseValidate extends CoreValidate
{
    protected $rule = [
        'page' => 'number',
        'limit' => 'number',
        'cc_number' => 'require',
        'operate' => 'number',
    ];
    
    protected $message = [
        'page.number' => PARAM_TYPE_ERROR,
        'limit.number' => PARAM_TYPE_ERROR,
        'cc_number.require' => 'cc_number不能为空',
        'operate.number' => PARAM_TYPE_ERROR,
    ];
    
    protected $scene = [
        'getscripts' => ['page','limit'],
        'getrecordurl' => ['cc_number','operate'],
    ];
}