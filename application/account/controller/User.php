<?php
/**
 *  业主管理界面
 *  
 *  业主注册方式有两种途径  
 *  
 * 一种 是物业直接添加 基本信息填写就行
 * 二种 是用户自主注册 注册需要填写相关信息
 *  
 */
namespace app\account\controller;
use core\controller\Account;
use think\Config;
use core\model\Operator;

class User extends Account
{
    /**
     * 首页 业主列表
     */
    
     public function index()
     {
         $page = $this->request->param("page");
         $offset = $this->request->has("offset")?$this->request->param("offset"):Config::get("default_pagesize");
         
         return $this->fetch();
     }
     
     /**
      * 审核用户
      */
     public function audit()
     {
         
     }
     
     /**
      * 添加用户
      */
     public function add()
     {
         
     }
     
     /**
      * 删除用户 
      * 仅仅支持物业管理员添加的用户 无法删除自主添加的用户
      */
     public function remove()
     {
         
     }
     
     
}