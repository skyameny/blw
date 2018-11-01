<?php
namespace app\api\controller;

use core\controller\Api;
use think\Controller;

//设置允许跨域
header("Access-Control-Allow-Origin: *");
header("Access-Control-Request-Headers:Origin, X-Requested-With, Content-Type, Accept,xfilecategory,xfilename,xfilesize");
header("Access-Control-Allow-Methods:*");

class Manager extends Api
{
    /**
     * 获取社区管理员用户
     */
    public function getManagers()
    {
        
       $list = array();
       $limit = $this->request->param("limit");
       $sort  = $this->request->param("sort");
       
       
       
       for($i =0 ;$i<100;$i++)
       {
           $origan_list = array();
           $origan_list["avatar"] = "https://gw.alipayobjects.com/zos/rmsportal/eeHMaZBwmTvLdIwMfBpg.png";
           $origan_list["callNo"] = 708+$i;
           $origan_list["createdAt"] = 1538992734;
           $origan_list["description"] = "这是一段描述";
           $origan_list["disabled"] = true;
           $origan_list["href"] = "https://ant.design";
           $origan_list["key"] = $i;
           $origan_list["no"] = "TradeCode $i";
           $origan_list["owner"] = "张三";
           $origan_list["progress"] = "35";
           $origan_list["status"] = 2;
           $origan_list["statusText"] = "商业管理员";
           $origan_list["statusType"] = "error";
           $origan_list["title"] = "管理员级别";
           $origan_list["updatedAt"] = 1538992734;
           $list[] = $origan_list;
       }
       return $list;exit;
       $this->result($list);
    }
       
    
}
