<?php
/**
 * session 存储器
 * User: keepwin100
 * Date: 2019-06-05
 * Time: 13:52
 */

namespace authority\includes\user\storage;

use authority\includes\user\AnonymousUser;
use authority\includes\user\GenerisUser;
use authority\includes\user\IdentifyUser;
use core\exception\CoreException;
use think\Session;

class SessionAuthStorage implements AuthStorage
{
    const PHP_SESSION_SESSION_KEY = 'bl_session_Session';

    private static $user = null;

    /**
     * @param IdentifyUser $identifyUser
     * @return bool|mixed
     */
    public function startStorage(IdentifyUser $identifyUser)
    {
        if(PHP_SAPI != 'cli'){
            if ($identifyUser instanceof GenerisUser) {
                Session::set(self::PHP_SESSION_SESSION_KEY, $identifyUser);
            }
        }
        return true;
    }

    /**
     * @return bool|mixed
     */
    public function endStorage()
    {
        Session::delete(self::PHP_SESSION_SESSION_KEY);
        return self::startStorage(new AnonymousUser());
    }

    /**
     * 获取登录用户
     * @return AnonymousUser|IdentifyUser|mixed|null
     * @throws CoreException
     */
    public function getStorageUser()
    {
        if(Session::has(self::PHP_SESSION_SESSION_KEY)) {
            $user = Session::get(self::PHP_SESSION_SESSION_KEY);
            if (! $user instanceof IdentifyUser) {
                throw new CoreException(STATUS_CODE_SYSTEM_ERROR,'Non session stored in php-session');
            }
            self::$user = $user;
        } else {
            self::$user = new AnonymousUser();
        }
        return self::$user;
    }
}

