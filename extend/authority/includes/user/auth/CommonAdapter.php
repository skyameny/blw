<?php
/**
 * Created by PhpStorm.
 * User: keepwin100
 * Date: 2019-06-05
 * Time: 23:05
 */
namespace authority\includes\user\auth;

use authority\includes\user\AnonymousUser;
use authority\includes\user\IdentifyUser;
use authority\includes\user\storage\AuthStorage;
use authority\service\UserService;
use core\exception\CoreException;

abstract class CommonAdapter implements Adapter
{
    /**
     * 会话机制
     * @var AuthStorage
     */
    protected  $storage;

    public  function setStorage(AuthStorage $storage){
        $this->storage = $storage;
    }

    /**
     * 登录{用户名密码}
     * @param array $params
     * @return bool
     * @throws CoreException
     */
    abstract public function login(array $params);

    public function storageState(IdentifyUser $user)
    {
        $this->storage->startStorage($user);
    }
    /**
     * 验证是否登录
     * @return bool
     */
    public  function authenticate()
    {
        $session = $this->storage->getStorageUser();
        return !($session instanceof AnonymousUser);
    }
    abstract public function checkParams(array $params);
}