<?php
/**
 * Created by PhpStorm.
 * User: keepwin100
 * Date: 2019-06-12
 * Time: 15:36
 */

namespace app\api\logic;


use authority\exception\MemberException;
use authority\includes\user\storage\DtAuthStorage;
use authority\service\IdentifyService;
use core\logic\Logic;

class PassportLogic extends  Logic
{
    protected $memberService;
    /**
     * @var IdentifyService
     */
    protected $identifyService;

    public function login($username,$password)
    {
        $params = [];
        $params["username"] = $username;
        $params["password"] = $password;
        $params["type"] = "app";
        $this->identifyService = IdentifyService::singleton();
        $this->identifyService->setStorage(new DtAuthStorage());
        $res = $this->identifyService->login($params);
        if(empty($res)){
            throw new MemberException(STATUS_CODE_LOGIN_FAILED);
        }
        return ["token"=>$res];
    }

}