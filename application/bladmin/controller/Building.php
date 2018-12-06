<?php
/*
 * -------------------------------------------------------------------------------------------
 * @Title        : 居住楼编号
 * @Version      : V1.0.0.2
 * @Initial-Time : 2018年11月
 * @auth         : Dream <1015617245@qq.com>
 * @Last-time    : 2018-11-09
 * @Desc         : 项目描述
 * -------------------------------------------------------------------------------------------
*/
namespace  app\admin\controller;

use core\controller\Admin;
use core\controller\tool\ApiPagination;
use core\service\ServiceManagement;
use app\admin\validate\BuildingValidate;

class Building extends Admin
{
    use ApiPagination;
 
    protected $validate = BuildingValidate::class;
    
    /**
     * 获取社区列表
     */
    public function getBuildings()
    {
        $question_service = ServiceManagement::singleton()->get("building");
        $results = $question_service->searchInstances($this->paginationParams());
        $this->result($results);
    }
    
    /**
     * 添加一个building
     */
    public function addBuilding()
    {
        $data = $this->filter(["name"=>"building_name","level"=>"building_level","unit","status","cid"=>"community_id"]);
        $building_service = ServiceManagement::singleton()->get("building");
        $result = $building_service->createInstance($data);
        if (empty($result)){
            $this->result("",STATUS_CODE_ADD_BUILDING_FAILED);
        }
        $this->result(["id"=>$result->getAttr("id")]);
    }
    
    /**
     * 编辑
     */
    public function editBuilding()
    {
        $data = $this->filter(["name"=>"building_name","level"=>"building_level","unit","status","cid"=>"community_id","id"=>"building_id"]);
        $building_service = ServiceManagement::singleton()->get("building");
        $result = $building_service->updateInstance($data);
        if (empty($result)){
            $this->result("",STATUS_CODE_EDIT_BUILDING_FAILED);
        }
        $this->result($result);
    }
    

    
    
}

