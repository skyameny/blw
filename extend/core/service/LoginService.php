<?php
namespace core\service;

use core\exception\AuthFailedException;
use core\includes\user\AuthAdapter;
use core\includes\session\DefaultSession;
use core\includes\user\User;
use core\includes\session\SessionManagement;
use Phinx\Db\Adapter\AdapterFactory;
use core\includes\user\auth\AuthFactory;
use think\Request;


class LoginService
{
    
    public static function login($userLogin, $userPassword)
    {
        
        try {
            $user = self::authenticate($userLogin, $userPassword);
            $loggedIn = self::startSession($user);
            //同步登录数据
            $request = Request::instance();
            $userResource = SessionManagement::getSession()->getUser()->getUserResource();
            $userResource->isUpdate(true)->save(["last_ip"=>$request->ip(),"last_login"=>NOW_TIME]);
            
        } catch (AuthFailedException $e) {
            $loggedIn = false;
        }
        return $loggedIn;
    }
    
    /**
     * 
     * @param string $userLogin
     * @param string $userPassword
     * @throws LoginFailedException
     * @return User
     */
    public static function authenticate($userLogin, $userPassword)
    {
        $user = null;
        
        $adapter = AuthFactory::creatadapter();
        $exceptions = array();
        
        $adapter->setCredentials($userLogin, $userPassword);
        try {
            $user = $adapter->authenticate();
        } catch (AuthFailedException $exception) {
            //throw new AuthFailedException($exception);
        }
        
        if (!is_null($user)) {
            return $user;
        } else {
            throw new AuthFailedException($exception);
        }
    }
    
    
    /**
     * Start a session for a provided user
     * 
     * @param common_user_User $user
     * @return boolean
     */
    public static function startSession(User $user)
    {
        $session = new DefaultSession($user);
        SessionManagement::startSession($session);
        return true;
    }
}