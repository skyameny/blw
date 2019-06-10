<?php
namespace authority\includes\user\auth;

use authority\includes\user\IdentifyUser;
use authority\includes\user\storage\AuthStorage;

interface Adapter
{
    /**
     * @param array $params
     * @return bool
     */
    public function login(array $params);

    public function setStorage(AuthStorage $storage);

    /**
     * @param IdentifyUser $user
     * @return bool
     */
    public function storageState(IdentifyUser $user);

    public function checkParams(array $params);
}