<?php
namespace authority\includes\user;

use authority\model\Role;
use authority\model\User;
use  think\model;

interface UserManagement
{
    /**
     * 是否存在用户名
     *
     * @param $login
     * @param model|null $class
     * @return mixed
     */
    public function loginExists($login);

    public function mobileExists($mobile);

    /**
     * 添加用户 必须指明 用户 密码 mobile
     * @param $username
     * @param $pwd
     * @param $mobile
     * @param array $role
     * @return mixed
     */
    public function addUser($username,$pwd,$mobile,$role = []);

    /**
     * 删除用户
     * @param Model $user
     */
    public function removeUser(User $user);

    /**
     * 根据用户名查找用户
     * @param  $login
     * @param UserModel $class
     */
    public function getOneUser($login);

    /**
     * 禁止
     * @param model $user
     * @return mixed
     */
    public function disableUser(User $user);

    /**
     * 允许
     * @param model $user
     * @return mixed
     */
    public function enableUser(User $user);

    /**
     * 密码是否正确
     * @param  $password
     * @param Model $user
     */
    public function isPasswordValid($password,  User $user);

    /**
     * 设置密码
     * @param Model $user
     * @param  $password
     */
    public function setPassword( User $user, $password);

    /**
     * 获取用户的角色
     * @param Model $user
     */
    public function getUserRoles( User $user);

    /**
     * 是否含有角色
     * @param Model $user
     * @param  $role
     */
    public function userHasRoles( User $user, Role $role);

    /**
     * 绑定角色
     * @param User $user
     * @param Model $role
     * @return bool
     */
    public function attachRole( User $user,  Model $role);

    /**
     * 解除绑定角色
     * @param Model $user
     * @param Model $role
     */
    public function unAttachRole( User $user,  Model $role);

    /**
     * 是否管理员
     * @param User $user
     * @return mixed
     */
    public function isAdmin(User $user);
}