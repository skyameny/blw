<?php
namespace core\includes\user;

use  think\model;
use  core\model\User as UserModel;

interface UsersManagement
{
    /**
     * 是否存在用户名
     *
     * @param $login
     * @param model|null $class
     * @return mixed
     */
    public function loginExists($login,  Model $class = null);

    /**
     * 添加用户
     * @param array $user_data
     * @param model|null $role
     * @return mixed
     */
    public function addUser($user_data,  Model $role = null);

    /**
     * 删除用户
     * @param Model $user
     */
    public function removeUser(Model $user);

    /**
     * 根据用户名查找用户
     * @param  $login
     * @param UserModel $class
     */
    public function getOneUser($login,  Model $class = null);

    /**
     * 禁止
     * @param model $user
     * @return mixed
     */
    public function disable(Model $user);

    /**
     * 允许
     * @param model $user
     * @return mixed
     */
    public function enable(Model $user);

    /**
     * 是否使用session登录
     */
    public function isASessionOpened();

    /**
     * 密码是否正确
     * @param  $password
     * @param Model $user
     */
    public function isPasswordValid($password,  Model $user);

    /**
     * 设置密码
     * @param Model $user
     * @param  $password
     */
    public function setPassword( Model $user, $password);

    /**
     * 获取用户的角色
     * @param Model $user
     */
    public function getUserRoles( Model $user);

    /**
     * 是否含有角色
     * @param Model $user
     * @param  $role
     */
    public function userHasRoles( Model $user, $role);

    /**
     * 绑定角色
     * @param UserModel $user
     * @param Model $role
     */
    public function attachRole( UserModel $user,  Model $role);

    /**
     * 解除绑定角色
     * @param Model $user
     * @param Model $role
     */
    public function unnatachRole( Model $user,  Model $role);

    /**
     * 获取该用户可用的角色
     */
    public function getAllowedRoles();

    /**
     * 获取默认觉得
     */
    public function getDefaultRole();

}

/**
 * end  UsersManagement
 */