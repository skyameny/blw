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
use core\validate\BlValidate;
use think\Db;
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
     * 设置头像
     * @param User $user
     * @param $avatar
     * @return false|int
     */
    public function setAvatar(User $user,$avatar)
    {
        return $user->save(["avatar"=>$avatar]);
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
     * 修改用户
     * 只能更换 nickname mobile telphone password
     * @param User $user
     * @param $data
     * @return bool
     * @throws UserException
     * @throws \Throwable
     */
    public function modifyUser(User $user, $data)
    {
        $saveData = [];
        if (empty($data)) {
            throw new UserException(STATUS_CODE_USER_MODIFY_FAILED);
        }
        if (isset($data["mobile"])) {
            if ($data["mobile"] != $user->getAttr("mobile")
                && $this->mobileExists($data["mobile"])) {
                throw new UserException(STATUS_CODE_MOBILE_EXITS);
            }
            $saveData["mobile"] = $data["mobile"];
        }
        if (isset($data["nickname"])) {
            $saveData["nickname"] = $data["nickname"];
        }
        if (isset($data["telphone"])) {
            $saveData["telphone"] = $data["telphone"];
        }
        if(isset($data["password"])){
            if(!BlValidate::isPassword($data["password"])){
                throw  new  UserException(STATUS_CODE_NONSTANDARD_PASSWORD);
            }
            $saveData["password"] = $this->getPasswordHash()->encrypt($data["password"]);
        }
        Db::startTrans();
        try {
            $flag = $user->isUpdate(true)->save($saveData);
            if ($flag === false) {
                throw new UserException(STATUS_CODE_USER_MODIFY_FAILED);
            }
            #角色操作
            if (isset($data["roles"])) {
                $role_service = RoleService::singleton();
                $currentRoles = $user->roles;
                #已经记录的role_ids
                $tpis_roleids = [];
                foreach ($currentRoles as $c_role) {
                    if (!in_array($c_role->getAttr("id"), $data["roles"])) {
                        $this->unAttachRole($user, $c_role);
                    } else {
                        $tpis_roleids[] = $c_role->getAttr("id");
                    }
                }
                foreach ($data["roles"] as $n_roleid) {
                    if (!in_array($n_roleid, $tpis_roleids)) {
                        $role = $role_service->getRoles(["id" => $n_roleid]);
                        if (empty($role)) {
                            //不存在的角色ID
                            throw new UserException(STATUS_CODE_ROLE_NOT_EXISTS);
                        }
                        $this->attachRole($user, current($role));
                    }
                }
            }
            Db::commit();
        } catch (\Throwable $e) {
            ExLog::log("角色配置失败正在回滚[" . $e->getCode() . "]");
            Db::rollback();
            throw $e;
        }
        return true;
    }

    /**
     * 禁用
     * @param User $user
     * @return false|int|mixed
     */
    public function disableUser(User $user)
    {
        return $user->save(["status"=>User::STATUS_DISABLE]);
    }

    /**
     * 启用
     * @param User $user
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
        return $user->isUpdate(true)->save(["passwd"=>$passwordHash]);
    }

    public function setMobile(User $user, $mobile)
    {
        if ($mobile != $user->getAttr("mobile")
            && $this->mobileExists($mobile)) {
            throw new UserException(STATUS_CODE_MOBILE_EXITS);
        }
        $user->isUpdate(true)->save(["mobile"=>$mobile]);
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
        return $user->getAttr("type") == 0;
    }

}