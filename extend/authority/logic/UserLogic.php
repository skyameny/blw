<?php
/**
 * 用户-逻辑层
 * @author 飞雪蓑笠翁
 */
namespace authority\logic;

use authority\exception\PasswordException;
use authority\exception\UserException;
use authority\service\RoleService;
use authority\service\UserService;
use core\logic\Logic;
use core\utils\ExLog;
use core\validate\BlValidate;

class UserLogic extends Logic
{
    /**
     * @var UserService
     */
    protected $userService;
    /**
     * @var RoleService
     */
    protected $roleService;

    /**
     * 获取用户列表
     * @param array $params
     * @return array
     */
    public function getUsersList($params = [])
    {
        $this->userService = UserService::singleton();
        $users = $this->userService->getUsers($params);
        return $users;
    }

    /**
     * 添加用户
     * @param $data
     * @return bool
     * @throws UserException
     */
    public function addUserByData($data)
    {
        $this->roleService = RoleService::singleton();
        $this->userService = UserService::singleton();
        $username = $data["register_username"];
        $pwd = $data["register_password"];
        $mobile = $data["register_mobile"];
        $roles_id = $data["roles"];
        $gid = isset($data["gid"])?intval($data["gid"]):0;
        $roles = [];
        foreach ($roles_id as $role_id){
            $roles[] = $this->roleService->getRoles(["id"=>$role_id])[0];
        }
        #验证密码规则
        try {
            $helper_password = $this->userService->getPasswordHash();
            $pwdHash = $helper_password->encrypt($pwd);
        }catch (PasswordException $e){
            ExLog::log("密码不符合规则[".$e->getCode()."]:".$e->getMessage());
            throw new UserException($e->getCode());
        }
        #添加到数据库
        try{
             $user= $this->userService->addUser($username,$pwdHash,$mobile,$gid);
        }
        catch (UserException $e){
            ExLog::log("添加失败[".$e->getCode()."]:".$e->getMessage());
            throw $e;
        }
        #设置用户其他属性
        if(isset($data["avatar"])){
            if(!$this->userService->setAvatar($user,$data["avatar"])){
                ExLog::log("设置头像失败[".$data["avatar"]."]");
                $this->userService->removeUser($user);
                throw new UserException(STATUS_CODE_USER_ADD_FAILED);
            };
        }

        #绑定角色
        foreach ($roles as $role){
            $res = $this->userService->attachRole($user,$role);
            if(!$res){
                $this->userService->removeUser($user);
                return false;
            }
        }
        return true;
    }

    /**
     * 修改用户 本函数仅仅更新字段 密码等操作需要独立操作
     *
     * @param $params
     * @return bool
     */
    public function modifyUserByData($params)
    {
        $uid = $params["uid"];
        $this->userService = UserService::singleton();
        $user = $this->userService->getUsers(["id"=>$uid]);
        if(empty($user)){
            throw  new  UserException(STATUS_CODE_USER_NOT_EXITS);
        }
        $modify_data = [];
        if(isset($params["nickname"])){
            $modify_data["nickname"] = $params["nickname"];
        }
        if(isset($params["mobile"])){
            if(!BlValidate::isMobile($params["telphone"])){
                throw  new  UserException(STATUS_CODE_MOBILE_NO_RIGHT);
            }
            $modify_data["mobile"] = $params["mobile"];
        }
        if(isset($params["password"])){
            $modify_data["password"] = $params["password"];
        }
        if(isset($params["telphone"])){
            $modify_data["telphone"] = $params["telphone"];
        }
        if(isset($params["roles"])){
            $modify_data["roles"] = $params["roles"];
        }
        $user = current($user);
        return $this->userService->modifyUser($user,$modify_data);
    }

    /**
     * 用户禁用
     *
     * @param $id
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function disAbleUserById($id){
        $this->userService = UserService::singleton();
        $user = $this->userService->getUsers(["id"=>$id]);
        if (empty($user)){
            throw  new  UserException(STATUS_CODE_USER_NOT_EXITS);
        }
        $result = $this->userService->disableUser(current($user));
        return $result;
    }
    public function enAbleUserById($id){
        $this->userService = UserService::singleton();
        $user = $this->userService->getUsers(["id"=>$id]);
        $result = $this->userService->enableUser(current($user));
        return $result;
    }

    /**
     * 批量删除用户
     * @param $uids
     * @return bool
     */
    public function deleteUserByIds($uids)
    {
        $this->userService = UserService::singleton();
        foreach ($uids as $uid){
            $user = $this->userService->getUsers(["id"=>$uid]);
            if(empty($user)){
                throw new UserException(STATUS_CODE_USER_NOT_EXITS,"用户不存在[ID:$uid]");
            }
            $this->userService->removeUser(current($user));
        }
        return true;
    }
}