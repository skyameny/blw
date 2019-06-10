<?php
/**
 * 定义IdentifyService 接口
 * User: keepwin100
 * Date: 2019-06-06
 * Time: 15:07
 */
namespace authority\includes\user;

use authority\includes\user\storage\AuthStorage;

interface IdentifyManagement
{
    /**
     * 提供登录入口
     * @param $params
     * @return mixed
     */
    public function login($params);

    /**
     * 提供退出入口
     * @return mixed
     */
    public function logout();

    /**
     * 提供验证入口
     * @return mixed
     */
    public function authenticate();

    /**
     * 获取存取器
     * @return AuthStorage
     */
    public function getStorage();

    /**
     * 提供获取获取登录用户
     * @return mixed
     */
    public function getIdentifyUser();

}