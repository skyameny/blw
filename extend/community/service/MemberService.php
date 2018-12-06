<?php
/**
 * 基本用户类
 * User: keepwin100
 * Date: 2018-12-05
 * Time: 17:30
 */
namespace  community\service;
use community\model\Member;
use core\service\Service;


class MemberService extends  Service
{
    /**
     * 获取资源列表
     *
     * @param array $condition
     * @return mixed
     */
    public function searchInstances($condition = []){
        $resource_model = new Member();
        return $resource_model->searchInstances($condition);
    }









}