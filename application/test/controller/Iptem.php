<?php
/*
 * -------------------------------------------------------------------------------------------
 * @Title        : 文件标题
 * @Version      : V1.0.0.2
 * @Initial-Time : 2018年11月
 * @auth         : Dream <1015617245@qq.com>
 * @Last-time    : 2018-11-09
 * @Desc         : 项目描述
 * -------------------------------------------------------------------------------------------
*/
namespace  app\test\controller;

use think\Controller;
use core\model\Iptem as IptemModel;

class Iptem extends Controller
{
    public function auth()
    {
        $result = null;
        try {
            $result = IptemModel::Auth($this->request->ip(),5);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            echo $msg;
        }

        IptemModel::clear($this->request->ip());
    }
    
    
}
