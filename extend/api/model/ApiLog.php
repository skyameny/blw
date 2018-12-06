<?php
/**
 * API log
 * @author Dream
 * 
 */

namespace  api\model;


use core\model\BlModel;

class ApiLog extends BlModel
{
    const STATUS_RESULT_SUCCESS = 1;//成功
    
    const STATUS_RESULT_EXCEPTION = 0; //异常
    
    
}