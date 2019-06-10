<?php
/**
 * 认证控制
 * 每种用户登录需要自行实现一套登录机制
 * User: 飞雪蓑笠翁 Dream
 * Date: 2019-06-02
 * Time: 12:39
 */
namespace authority\service;
use authority\exception\AuthFailedException;
use authority\includes\user\AnonymousUser;
use authority\includes\user\auth\Adapter;
use authority\includes\user\auth\AuthFactory;
use authority\includes\user\IdentifyManagement;
use authority\includes\user\storage\AuthStorage;
use core\model\SystemConfig;
use core\service\Service;

class  IdentifyService extends Service implements IdentifyManagement
{
    /**
     * 登录器
     * @var Adapter
     */
    protected $adapter;
    protected $storage;

    /**
     * @param string $type
     * @return \authority\includes\user\auth\AccountAdapter|Adapter|null
     */
    protected function getAdapterByType($type="")
    {
        if(is_null($this->adapter)){
            if(empty($type)){
                $type = "account";
            }
            $this->adapter = AuthFactory::createAdapter($type);
        }
        $this->adapter->setStorage($this->getStorage());
        return $this->adapter;
    }

    /**
     * @param $params
     * @return bool
     * @throws \core\exception\CoreException
     */
    public function login($params)
    {
        try {
            $loggedIn = $this->getAdapterByType($params["type"])->login($params);
        } catch (AuthFailedException $e) {
            $loggedIn = false;
        }
        return $loggedIn;
    }

    public function getStorage()
    {
        if(is_null($this->storage)) {
            $type = SystemConfig::getValue("common_storage_type");
            $this->setStorage(AuthFactory::createStorage($type));
        }
        return $this->storage;
    }

    /**
     * 定制storage
     * @param AuthStorage $storage
     */
    public function setStorage(AuthStorage $storage)
    {
        $this->storage = $storage;
    }
    /**
     * 退出登录
     * @param $user
     */
    public function logout(){
        $this->getStorage()->endStorage();
    }

    /**
     * 验证是否登录
     */
    public function authenticate()
    {
        $user = $this->getStorage()->getStorageUser();
        return !($user instanceof  AnonymousUser);
    }

    /**
     * 检查参数合法性 预判手机号
     * @param $params
     * @return mixed
     */
    public function checkParams($params)
    {
        return $this->getAdapterByType($params["type"])->checkParams($params);
    }

    /**
     * 获取登录用户
     * @return AnonymousUser|\authority\includes\user\IdentifyUser|mixed|null
     * @throws \core\exception\CoreException
     */
    public function getIdentifyUser()
    {
        $user = $this->getStorage()->getStorageUser();
        return $user;
    }
}