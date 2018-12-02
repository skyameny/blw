<?php
namespace core\model;

use think\Model;

class OperateLog extends BlModel
{
    
    protected $likeColumn = ["message","operator_url"];
    
    protected $name = "log";
    
    protected $createTime = false; //关闭自动格式化
    
}