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
namespace app\admin\controller;

use core\controller\Admin;
use core\controller\tool\ApiPagination;
use core\service\ServiceManagement;
use app\admin\validate\CommunityValidate;

class Family extends Admin
{
    use ApiPagination;
    
    protected $validate = CommunityValidate::class;
    
    protected $communityService = null;
    
    public function _initialize()
    {
        //$this->communityService = CommunityService::singleton(); //ServiceManagement::singleton()->get("community");
        parent::_initialize();
    }
    
    public function families()
    {
        $family_service = ServiceManagement::singleton()->get("family");
        $results = $family_service->searchInstances($this->paginationParams());
        $this->result($results);
    }
    
    /**
     * 
     */
    public function getInfo()
    {
        $family_service = ServiceManagement::singleton()->get("family");
        $result = $family_service->searchInstances(["id"=>$this->request->param("family_id")]);
        if(empty($result)){
            $this->result("",STATUS_CODE_FAMILY_NO_EXISTS);
        }
        $this->result($result[0]);
    }
    
    public function addFamily()
    {
        
    }
    
}