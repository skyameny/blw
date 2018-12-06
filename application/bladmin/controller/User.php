<?php 
/*
 +-------------------------------------------------------------------------------------------
 + Title        : 用户管理
 + Version      : V1.0.0.2
 + Initial-Time : 2018年11月09日
 + @auth Dream <1015617245@qq.com>
 + Last-time    : 2018-11-09
 + Desc         : 项目描述
 +-------------------------------------------------------------------------------------------
*/

namespace app\bladmin\controller;

use core\controller\Admin;
use core\service\UserService;
use core\controller\tool\ApiPagination;

class User extends Admin
{
    use ApiPagination;
    
    public function getUserService()
    {
        return UserService::singleton();
    }
    
    
    
    
    
    
}
