<?php
/**
 * 用户会话存储对象
 * User: keepwin100
 * Date: 2019-06-05
 * Time: 13:46
 */

namespace authority\includes\user\storage;


use authority\includes\user\IdentifyUser;

interface AuthStorage
{
    /**
     * 开始会话调用
     * @param IdentifyUser $identifyUser
     * @return mixed
     */
    public function startStorage(IdentifyUser $identifyUser);

    /**
     * 结束会话调用
     * @return mixed
     */
    public function endStorage();

    /**
     * 获取会话存储
     * @return mixed
     */
    public function getStorageUser();
}