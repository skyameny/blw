<?php
/**
 * Created by PhpStorm.
 * User: silkshadow
 * Date: 2019-05-14
 * Time: 14:14
 */

namespace authority\service;

use core\includes\user\User;
use core\model\Role;


class EpAuthorityService extends AuthorityService
{
    // 根据角色获取权限
    public function getRulesByRoles($roles){
        $rules = [];
        foreach ($roles as $role) {
            $authRule = $this->getAllRule($role);
            $rules = array_merge($rules,$authRule);
        }
        return $rules;
    }

    // 获取权限菜单栏
    public function getMenu($rules)
    {
        $tree = $this->authorityDao->getAuthorityByParentId($rules);
        return $tree;
    }

    // 获取角色分页
    public function getRolesByPagination($eid,$name,$paginationParams){
        if(strlen($name)>0){
            $result = $this->roleDao->getRolesSearchPageByEidAndName($eid, $name, $paginationParams);
        }else{
            $result = $this->roleDao->getRolesPageByEid($eid, $paginationParams);
        }
        return $result;
    }

    // 获取角色详情
    public function getRoleById($id){
        return $this->roleDao->getRoleById($id);
    }

    // 账户分页
    public function getUsersByPagination($eid,$name,$paginationParams){
        if(strlen($name)>0){
            $result = $this->userDao->getUsersSearchPageByEidAndName($eid, $name, $paginationParams);
        }else{
            $result = $this->userDao->getUsersPageByEid($eid, $paginationParams);
        }
        return $result;
    }
}