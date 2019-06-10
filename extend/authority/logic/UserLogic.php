<?php
/**
 * 用户-逻辑层
 * @author 飞雪蓑笠翁
 */
namespace authority\logic;

use authority\exception\PasswordException;
use authority\exception\UserException;
use authority\includes\helper\HelperPassword;
use authority\model\User;
use authority\service\RoleService;
use authority\service\UserService;
use core\logic\Logic;
use core\utils\ExLog;

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
        $username = $data["username"];
        $pwd = $data["pwd"];
        $mobile = $data["mobile"];
        $roles_id = $data["roles"];
        $gid = $data["gid"];
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
            if(!$this->userService->setAvatar($data["avatar"])){
                ExLog::log("设置头像失败[".$data["avatar"]."]");
                throw new UserException(USER_ADD_FAILED);
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
     * 修改用户
     * @param $params
     * @return bool
     */
    public function modifyUserByData($params)
    {
        return true;
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
        $result = $this->userService->disableUser($user);
        return $result;
    }
}