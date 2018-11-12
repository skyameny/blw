<?php
namespace  app\test\controller;

use think\Controller;
use core\includes\session\SessionManagement;

class Session extends Controller
{
    
    
    public function Index()
    {
        var_dump(SessionManagement::getSession());
    }
}