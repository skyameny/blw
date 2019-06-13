<?php
/**
 * APP 登录器
 * 使用的密码和用户名 使用数据库机制
 * User: keepwin100
 * Date: 2019-06-12
 * Time: 10:30
 */

namespace authority\includes\user\auth;


use authority\exception\AuthFailedException;
use authority\includes\user\MemberUser;
use community\service\MemberService;
use core\utils\ExLog;

class AppAdapter extends CommonAdapter
{
    protected $username;

    protected $password;

    /**
     * @var MemberService
     */
    protected  $memberService;

    public function __construct()
    {
        $this->memberService = MemberService::singleton();
    }

    public function getPasswordHash()
    {
        return $this->memberService->getPasswordHash();
    }

    public function login(array $params)
    {
        if(!isset($params["username"]) || empty($params["username"])){
            throw new AuthFailedException(STATUS_CODE_PARAM_ERROR);
        }
        if(!isset($params["password"]) || empty($params["password"])){
            throw new AuthFailedException(STATUS_CODE_PARAM_ERROR);
        }
        $this->username = $params['username'];
        $this->password = $params['password'];
        $identifyUser = $this->verification();
        $this->storage->startStorage($identifyUser);
        $token = $this->memberService->getMemberToken($identifyUser->getUserResource());
        return $token->visible(["access_token","expiry_time"])->toArray();
        //return true;
    }

    /**
     * 检查逻辑
     * @return MemberUser
     * @throws AuthFailedException
     */
    protected function verification()
    {
        $users = $this->memberService->getMembers(
            ["username" => $this->username, "status" => 1]
        );
        if (count($users) > 1) {
            ExLog::log("出现多个重名的用户:" . $this->username, ExLog::ERROR);
            throw new AuthFailedException(MULTIPLE_USERS_FOR_SAME_LOGIN, "Multiple Users found with the same login '" . $this->username . "'.");
        }
        if (empty($users)) {
            throw new AuthFailedException(STATUS_CODE_USER_NOT_FOUND, 'Unknown user "' . $this->username . '"');
        }
        $userResource = current($users);
        $hash = $userResource->getAttr("password");
        if (!$this->getPasswordHash()->verify($this->password, $hash)) {
            throw new AuthFailedException(STATUS_CODE_LOGIN_FAILED, "Invalid password for user '" . $this->username . '"');
        }
        return new MemberUser($userResource);
    }

    public function checkParams(array $params)
    {
        // TODO: Implement checkParams() method.
    }
}