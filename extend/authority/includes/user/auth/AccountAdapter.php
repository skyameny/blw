<?php
/**
 * 用户名密码登录版本
 */
namespace authority\includes\user\auth;

use authority\exception\AuthFailedException;
use authority\service\UserService;
use core\exception\CoreException;
use authority\includes\user\GenerisUser;
use core\utils\ExLog;

class AccountAdapter extends CommonAdapter
{
    public function getPasswordHash()
    {
        $this->userService = UserService::singleton();
        return $this->userService->getPasswordHash();
    }

    /**
     * 登录{用户名密码}
     * @param array $params
     * @return bool
     * @throws CoreException
     */
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
        return true;
    }

    public function checkParams(array $params)
    {
        $mobile = $params["mobile"];
        if(empty($mobile)){
            return false;
        }
        $userService = UserService::singleton();
        return $userService->mobileExists($mobile);
    }

    /**
     * Username to verify
     * 
     * @var string
     */
    private $username;
    
    /**
     * Password to verify
     * 
     * @var $password
     */
	private $password;
	
	/**
	 * 
	 * @param array $configuration
	 */
	public function setOptions(array $options) {
	    // nothing to configure
	}
	
	/**
	 * (non-PHPdoc)
	 * @see 
	 */
	public function setCredentials($login, $password) {
	    $this->username = $login;
	    $this->password = $password;
	}

    /**
     * @return GenerisUser
     * @throws AuthFailedException
     */
    public function verification()
    {
        $this->userService = UserService::singleton();
        $users = $this->userService->getUsers(
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
        $hash = $userResource->getAttr("passwd");
        if (!$this->getPasswordHash()->verify($this->password, $hash)) {
            throw new AuthFailedException(STATUS_CODE_LOGIN_FAILED, "Invalid password for user '" . $this->username . '"');
        }
        return new GenerisUser($userResource);
    }
}