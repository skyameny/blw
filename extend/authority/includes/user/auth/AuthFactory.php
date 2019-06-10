<?php
namespace authority\includes\user\auth;
use authority\includes\user\storage\SessionAuthStorage;
use core\model\SystemConfig;
use core\service\SystemService;

class AuthFactory
{
    /**
     * 创建登录器
     * @param $type
     * @return AccountAdapter|null
     */
    public static function createAdapter($type)
    {
        $adpater = null;
        if (!is_null($type)) {
            $clazz = __NAMESPACE__ . "\\".ucfirst($type)."Adapter";
            if (class_exists($clazz)) {
                $adpater = new $clazz();
            }
        }
        if (empty($adpater)) {
            $adpater = new AccountAdapter();
        }
        return $adpater;
    }

    /**
     * 创建会话存储storage
     * @param string $type
     * @return SessionAuthStorage|null
     */
    public static function createStorage($type="")
    {
        $storage = null;
        $ns = "authority\\includes\\user\\storage\\";
        if(empty($type)){
            return new SessionAuthStorage();
        }
        $clazz =$ns.ucfirst($type)."AuthStorage";
        if (class_exists($clazz)) {
            $storage = new $clazz();
        }
        return $storage;

    }
}