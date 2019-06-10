<?php
/**
 * 权限管理器
 * 操作：
 *  授权
 *  验证
 *  解除授权
 *  添加授权
 *  获取可执行菜单
 *  获取权限列表
 * User: keepwin100
 * Date: 2019-04-24
 * Time: 13:55
 */

namespace authority\service;

use core\includes\user\User;
use core\model\Role;

interface  AuthorityManagement
{
    /**
     * 验证当前用户是否可操作
     * @param User $user
     * @param $action
     * @return mixed
     */

    public static function authentication(Role $role, $action,$params=array());

    /**
     * 获取用户可用权限列表
     * @param User $user
     * @return mixed
     */
    public function getAuthRule(User $user);

    /**
     * 获取可用菜单
     * @param User $user
     * @return mixed
     */
    public function getMenu($roles);
}