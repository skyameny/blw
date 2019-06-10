<?php
/**
 * 权限管理
 * User: keepwin100
 * Date: 2019-04-23
 * Time: 11:01
 */
namespace core\includes\auth;

use core\includes\user\User;

interface  AuthorityManagement
{
    /**
     * 检查当前用户是否能访问该控制器
     * @param \core\includes\user\User $user
     * @param $action #控制器名称
     * @return boolean
     */
    public function authentication(User $user,$action);

    /**
     * 获取用户的权限列表
     * @param User $user
     * @return array
     */
    public function getAuthRule(User $user);

    /**
     * 设置用户权限
     * @param User $user
     * @param array $rule
     * @return mixed
     */
    #public function setAuthRule(User $user,$rule=array());

    /**
     * 获取权限菜单
     * @param User $user
     * @return array
     */
    public function getMenu(User $user);

}

