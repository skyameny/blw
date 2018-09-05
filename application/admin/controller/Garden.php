<?php
/**
 * 系统管理员
 * 社区开户管理
 */
namespace app\admin\controller;

use core\controller\Admin;
use think\Request;
use core\models\Garden as GardenModel;

class Garden extends Admin
{
    /**
     * 列表
     * @return mixed|string
     */
    public function Building()
    {
        $gardens = GardenModel::where('status',1)->paginate(10,false,[
            'type'     => 'Bootstrap4',
            'var_page' => 'page',
        ]);
        $this->assign("gardens",$gardens);
        return $this->fetch();
    }
    
    /**
     * 展示详细信息
     */
    public function showInfo(){
        if(!$this->request->isAjax()){
           //header("HTTP/1.1 403 Forbidden");
           //exit;
            //config("default_return_type","json");
        }
        $cid = $this->request->param("cid");
        if(empty($cid)){
            $this->result("",PARAM_ERROR,"社区ID不能为空");
        }
        $garden = GardenModel::get($cid);
        if(is_null($garden)){
            $this->result("",RESULT_NOT_EXSIT,"该社区ID不存在");
        }
        $this->assign("cinfo",$garden);
        return $this->fetch();
    }
    
    public function addGarden()
    {
        
    }
    
    public function removeGarden()
    {
        
    }
    
    
    
   
    
    
    
    
}
