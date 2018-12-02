<?php
/*
 * -------------------------------------------------------------------------------------------
 * @Title        : 资源型服务 
 * @Version      : V1.0.0.2
 * @Initial-Time : 2018年11月
 * @auth         : Dream <1015617245@qq.com>
 * @Last-time    : 2018-11-09
 * @pakg         : core
 * @Desc         : 本服务提供查询 新建 修改 删除等操作
 * -------------------------------------------------------------------------------------------
*/
namespace core\service;

use core\service\interf\Searchable;
use core\model\BlModel;
use core\exception\CommonException;

abstract class ResourceService extends Service implements Searchable
{
    /**
     * 
     * @var BlModel
     */
    protected $model = null;
    
    public function __construct()
    {
        
    }
    
    /**
     *
     * @param array $condition
     */
    public function searchInstances(array $condition=[])
    {
        if(method_exists($this->model, "searchInstances")){
            return $this->model->searchInstances($condition);
        }
        return $this->model->where($condition);
    }
    
    /**
     * 删除资源
     */
    public function deleteInstance($resources=[])
    {
        //删除
        foreach ($resources as $resource){
            $resource->delete();
        }
        return true;
    }
    
    /**
     * 复制资源
     * @param unknown $resource
     */
    public function copyInstance($resource)
    {
        
    }
    
    /**
     * 创建一个新的资源
     * @param unknown $data
     * @param unknown $field
     * @return unknown
     */
    public function createInstance($data,$field=null)
    {
       return  $this->model::create($data,$field);
    }
    
    /**
     * 保存
     * 
     * @param unknown $data
     * @throws CommonException
     * @return unknown
     */
    public function updateInstance($data)
    {
        if(!isset($data["id"]))
        {
            throw new CommonException("",STATUS_CODE_ON_COMMUNITY_ID);
        }
        $community = $this->model::get($data["id"]);
        if(empty($community))
        {
            throw new CommonException("",STATUS_CODE_ON_COMMUNITY_ID);
        }
        return $community->save($data);
    }
}