<?php
/**
 * @author Dream
 * 角色管理
 */
namespace core\includes\user;
use think\model;

interface RolesManagement
{
    /**
     * 添加角色
     * @param $title
     * @param string $description
     * @param int $eid
     * @param int $type
     * @return mixed
     */
    public function addRole($title, $description="", $eid=0,$type=1);

    /**
     * 删除角色
     * @param Model $role
     * @return bool
     */
    public function removeRole(Model $role);

    /**
     * 获取子角色   无效实现
     * @param model $role
     * @return mixed
     */
    #public function getIncludedRoles(Model $role);

    /**
     * 创建子角色
     * @param model $role
     * @param array $roleToInclude
     * @return mixed
     */
    #public function includeRole(Model $role, array $roleToInclude);

    /**
     * 解除子角色
     *
     */
    #public function unincludeRole(Model $role,  $roleToUninclude);

    /**
     * 获取全部角色
     */
    public function getAllRoles();

}
/** end  role */