<?php
/**
 * 角色服务[Lower]
 * User: keepwin100
 * Date: 2019-05-14
 * Time: 18:47
 */

namespace authority\service;

use authority\exception\RoleException;
use authority\includes\user\RoleManagement;
use authority\model\AuthRule;
use authority\model\Role;
use core\service\Service;
use core\utils\ExLog;

class RoleService extends Service implements RoleManagement
{

    protected $roleModel = null;

    protected function __construct()
    {
        $this->roleModel = new Role();
        parent::__construct();
    }

    public function getRoles($condition)
    {
        $role_model = new Role();
        return $role_model->searchInstances($condition);
    }

    public function addRole($title, $remark = "", $gid = 0)
    {
        if($this->getOneRole($title))
        {
            throw new RoleException(STATUS_CODE_ROLE_NAME_EXISTS);
        }
        $data = [];
        $data["name"] = $title;
        $data["remark"] = $remark;
        if(!empty($gid)){
            $data["gid"] = $gid;
        }
        $role_model = new Role();
        $flag = $role_model->save($data);
        if(!$flag){
            throw new RoleException(STATUS_CODE_ADD_ROLE_FAILED);
        }
        ExLog::log("添加角色：".$role_model->getLastSql(),ExLog::INFO);
        return $role_model;
    }

    public function modifyRole(Role $role,$name,$remark){
        $data = [];
        $data["name"] = $name;
        $data["remark"] = $remark;
        $role->isUpdate(true)->save($data);
        ExLog::log("添加角色：".$role->getLastSql(),ExLog::INFO);
        return $role;
    }


    public function disableRole(Role $role)
    {
        return $role->save(["status"=>Role::STATUS_DISABLE]);
    }

    public function enableRole(Role $role)
    {
        return $role->save(["status"=>Role::STATUS_ENABLE]);
    }

    public function getOneRole($title)
    {
        $roles = $this->getRoles(["name"=>$title]);
        if(empty($roles)){
            return false;
        }else{
            return $roles[0];
        }
    }

    public function grantAuth(Role $role, AuthRule $auth)
    {

    }

    public function revokeAuth(Role $role, AuthRule $auth)
    {
        // TODO: Implement revokeAuth() method.
    }

    public function getRoleAuthTree(Role $role)
    {
        $authorityList =[];
        foreach ($role->authRule() as $auth){
            if($auth->getAttr("parent_id") ==0){
                $authorityList[] = $auth->visible(['id','name','title'])->toArray();
            }
        };
    }

    public function isAdminRole(Role $role)
    {
        return ($role->getAttr("gid") == 0  && $role->getAttr("id") != Role::EP_ADMIN_ID);
    }

    public function hasAuth(Role $role, AuthRule $auth)
    {
        // TODO: Implement hasAuth() method.
    }

    public function removeRole(Role $role)
    {
        // TODO: Implement removeRole() method.
    }

    /**
     * 扩展接口
     * @param Role $role
     * @param $rules
     */
    protected function setCacheRoleAuth(Role $role,$rules)
    {

    }

    protected function getCacheRoleAuth(Role $role){

    }

    /**
     * 更新权限缓存
     * @param Role $role
     */
    public function refreshRoleAuth(Role $role){

    }
}