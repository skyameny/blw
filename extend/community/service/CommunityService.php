<?php
namespace community\service;

use community\model\Community;
use core\service\Service;
use core\exception\CommonException;

class CommunityService extends  Service
{
    
    /**
     * 查询列表
     * 
     * @param array $condition
     * @return boolean|\think\static[]|\think\false
     */
    public function getCommunities($condition = [])
    {
        $sc_model = new Community();
        return $sc_model->searchInstances($condition);
    }
    /**
     * 创建新的社区
     * @param array $data
     * @return number|\think\false
     */
    public function createCommunity($data=[])
    {
        $search_community = $this->searchInstances(["name"=>$data["cname"]]);
        if(!empty($search_community)){
            throw new CommonException("",STATUS_CODE_COMMUNITY_AREADY_EXISTS);
        }
        $sc_model = new Community();
        return $sc_model->save($data);
    }
    
    /**
     * 注销
     * @param Community $community
     */
    public function deleteCommunity(Community $community)
    {
        
    }
    
}