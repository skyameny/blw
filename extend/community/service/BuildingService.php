<?php
/*
 * -------------------------------------------------------------------------------------------
 * @Title        : 居民楼管理
 * @Version      : V1.0.0.2
 * @Initial-Time : 2018年11月
 * @auth         : Dream <1015617245@qq.com>
 * @Last-time    : 2018-11-09
 * @Desc         : 项目描述
 * -------------------------------------------------------------------------------------------
*/
 namespace community\service;
 
 use core\service\Service;

use core\service\ResourceService;
use community\model\Building;
use core\utils\ExLog;
use core\exception\CommonException;
                        
 class BuildingService extends  ResourceService
 {
     public function __construct()
     {
         $this->model = new Building();
     }

     /**
      * 创建实例
      * 
      * {@inheritDoc}
      * @see \core\service\ResourceService::createInstance()
      */
     public  function createInstance($data,$field=null)
     {
         $condition = array();
         $condition["name"] = $data["name"];
         $condition["cid"]  = $data["cid"];
         if(!$this->searchInstances($condition)){
             return parent::createInstance($data);
         }
         ExLog::log("存在相同内容,无法创建",ExLog::INFO);
         throw new CommonException("存在相同的名称",STATUS_CODE_BUILDING_NAME_EXISTS);
     }
     
     
     
     
 }