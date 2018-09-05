<?php
/**
 * 系统管理员
 */
namespace app\admin\controller;

use core\controller\Admin;
use think\Request;
use core\models\SystemConfig;

class Setting extends Admin
{
    /**
     * 系统设置
     */
    public function System()
    {
        $configs = SystemConfig::all();
        $this->assign("configs",$configs);
        return $this->fetch();
    }
    
    
   
    
    
    
    
}
