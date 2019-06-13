<?php
/**
 * 角色逻辑层.
 * User: 飞雪蓑笠翁
 * Date: 2019-06-03
 * Time: 23:16
 */
namespace authority\logic;

use authority\exception\RoleException;
use authority\service\IdentifyService;
use authority\service\RoleService;
use core\logic\Logic;

class RoleLogic extends Logic
{
    /**
     * @var RoleService
     */
    protected $roleService;

    public function getGarden()
    {
        $identifyUser = IdentifyService::singleton()->getIdentifyUser();
        $garden = $identifyUser->getGardenResource();
    }


    public function getRolesByParams($params)
    {
        $this->roleService = RoleService::singleton();
        $result = $this->roleService->getRoles($params);
        return $result;
    }
    /**
     * 添加角色
     */
    public function addRole($name,$remark,$auth_ids)
    {
        $gid = 0;
        $this->roleService = RoleService::singleton();
        $role = $this->roleService->addRole($name,$remark,$gid);
        $this->roleService->disableRole($role);
        return true;
    }





    public function editRole($rid,$name,$remark)
    {
        $this->roleService = RoleService::singleton();
        $roles = $this->roleService->getRoles(["id"=>$rid]);
        if(empty($roles)){
            throw new RoleException(STATUS_CODE_ROLE_NOT_EXISTS);
        }
        $role = $roles[0];
        if($role->getAttr("type") ==0){
            throw new RoleException(STATUS_CODE_ROLE_EDIT_DISABLE);
        }
        $role = $this->roleService->modifyRole($role,$name,$remark);
        return true;

    }



}