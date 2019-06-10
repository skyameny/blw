<?php
/**
 * 用户管理
 * User: keepwin100
 * Date: 2019-06-01
 * Time: 22:47
 */
namespace authority\service;
use authority\exception\PasswordException;
use authority\exception\UserException;
use authority\includes\helper\HelperPassword;
use authority\includes\user\UserManagement;
use authority\model\Role;
use authority\model\User;
use core\service\Service;
use core\utils\ExLog;
use think\model;

class UserService extends Service  implements UserManagement
{
    /**
     * @var User
     */
    protected $userModel = null;

    protected function __construct()
    {
        $this->userModel = new User();
        parent::__construct();
    }

    /**
     * 获取密码加密对象
     * @return HelperPassword
     */
    public function getPasswordHash()
    {
        $helper =  new HelperPassword("sha1",5);
        return $helper;
    }

    /**
     * 查询用户列表
     * @param array $condition
     * @return array|mixed
     */
    public function getUsers($condition=[])
    {
        return $this->userModel->searchInstances($condition);
    }

    /**
     * @param $username
     * @param $pwd
     * @param $mobile
     * @param int $gid
     * @return User|mixed
     * @throws UserException
     */
    public function addUser($username,$pwd,$mobile,$gid=0)
    {
        if(!empty($username) && $this->loginExists($username)){
            throw new UserException(STATUS_CODE_LOGIN_EXITS);
        }
        if(!empty($mobile) && $this->mobileExists($mobile)){
            throw new UserException(STATUS_CODE_MOBILE_EXITS);
        }
        $user_model = new User();
        $flag = $user_model->save([
                "username"=>$username,
                "passwd"=>$pwd,
                "mobile"=>$mobile,
                "status"=>User::STATUS_ENABLE,
                "nickname"=>$username,
                "create_time"=>NOW_TIME,
                "gid"=>$gid
        ]);
        if(!$flag){
            throw new UserException(STATUS_CODE_USER_ADD_FAILED);
        }
        return $user_model;
    }

    /**
     * 禁用
     * @param model $user
     * @return false|int|mixed
     */
    public function disableUser(User $user)
    {
        return $user->save(["status"=>User::STATUS_DISABLE]);
    }

    /**
     * 启用
     * @param model $user
     * @return false|int|mixed
     */
    public function enableUser(User $user)
    {
        return $user->save(["status"=>User::STATUS_ENABLE]);
    }

    /**
     * 绑定关系
     * @param User $user
     * @param model $role
     * @return bool
     */
    public function attachRole(User $user, model $role)
    {
        return !!$user->roles()->save($role);
    }

    /**
     * 解除绑定关系
     * @param User $user
     * @param model $role
     * @return bool
     */
    public function unAttachRole(User $user, model $role)
    {
        return !!$user->roles()->detach([$role->getAttr("id")]);
    }

    public function loginExists($login)
    {
        $user = $this->getUsers(["username"=>$login]);
        return !empty($user);
    }

    public function mobileExists($mobile)
    {
        $user = $this->getUsers(["mobile"=>$mobile]);
        return !empty($user);
    }

    public function isPasswordValid($password, User $user)
    {
        $helperPassword = new HelperPassword("", 5);
        $result = $helperPassword->verify($password, $user->getAttr("passwd"));
        return !!$result;
    }

    /**
     * 删除用户 解除关联
     * @param User $user
     * @return int
     */
    public function removeUser(User $user)
    {
        $roles = $this->getUserRoles($user);
        foreach ($roles as $role){
            $this->unAttachRole($user,$role);
        }
        ExLog::log("删除用户[".$user->getAttr("username")."]",ExLog::INFO);
        return $user->delete();
    }

    /**
     * 设置密码 用于修改密码 设置密码
     * @param User $user
     * @param $password
     * @return bool|false|int
     */
    public function setPassword(User $user, $password)
    {
        try {
            $helperPassword = new HelperPassword("", 5);
            $passwordHash = $helperPassword->encrypt($password);
        }catch (PasswordException $e){
            return false;
        }
        return $user->save(["passwd"=>$passwordHash]);
    }

    public function getUserRoles(User $user)
    {
       return $user->roles();
    }

    public function userHasRoles(User $user, Role $role)
    {
        $roles  =  $this->getUserRoles($user);
        foreach ($roles as $c_role){
            if($role->isEqual($c_role)){
                return true;
            }
        }
        return false;
    }

    public function getOneUser($login)
    {
        $user_model = new User();
        $users = $user_model->searchInstances(["usename"=>$login]);
        if(empty($users)){
            return false;
        }
        return $users[0];
    }

    public function isAdmin(User $user){
        return $user->getAttr("type") ==0;
    }

}