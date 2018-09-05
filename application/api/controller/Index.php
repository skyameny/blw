<?php
namespace app\api\controller;

use core\controller\Api;
use core\controller\Base;
use think\Controller;

class Index extends Api
{
    public function index()
    {
        //echo "dede";
        $this->result("ok");
    }
}
