<?php
/**
 * @author Dream
 * 角色管理
 */
namespace authority\includes\user;
use authority\exception\RoleException;
use authority\model\AuthRule;
use authority\model\Role;
use think\model;

interface RoleManagement
{
    /**
     * 添加角色
     * @param $title
     * @param string $description
     * @param int $gid
     * @param int $type
     * @throws RoleException
     * @return mixed
     */
    public function addRole($title, $description="", $gid=0);

    /**
     * 删除角色
     * @param Model $role
     * @return bool
     */
    public function removeRole(Role $role);

    /**
     * 获取权限树
     * @param Role $role
     * @return mixed
     */
    public function getRoleAuthTree(Role $role);

    /**
     * 禁用
     * @param Role $role
     * @return bool
     */
    public function disableRole(Role $role);

    /**
     * 启用
     * @param Role $role
     * @return bool
     */
    public function enableRole(Role $role);

    /**
     * 根据名称获取角色
     * @param $title
     * @return mixed
     */
    public function getOneRole($title);

    /**
     * 给角色授权
     * @param Role $role
     * @param AuthRule $auth
     * @return mixed
     */
    public function grantAuth(Role $role,AuthRule $auth);

    /**
     * 移除角色授权
     * @param Role $role
     * @param AuthRule $auth
     * @return mixed
     */
    public function revokeAuth(Role $role,AuthRule $auth);

    /**
     * 是否有指定权限
     * @param Role $role
     * @param AuthRule $auth
     * @return mixed
     */
    public function hasAuth(Role $role,AuthRule $auth);

    /**
     * 是否管理员权限
     * @param Role $role
     * @return mixed
     */
    public function isAdminRole(Role $role);
}
/** end  role */