<?php
/**
 * Created by PhpStorm.
 * User: keepwin100
 * Date: 2019-06-05
 * Time: 14:55
 */
namespace authority\logic;

use authority\exception\AuthFailedException;
use authority\includes\user\auth\AuthFactory;
use authority\service\IdentifyService;
use core\logic\Logic;
use core\utils\ExLog;
use core\utils\helper\HelperVerificationCode;

class IdentifyLogic extends Logic
{
    /**
     * @var IdentifyService
     */
    protected $identifyService;

    /**
     * 用户登录
     * @param $username
     * @param $password
     * @return mixed
     * @throws AuthFailedException
     * @throws \core\exception\CoreException
     */
    public function login($username,$password)
    {
        $this->identifyService = IdentifyService::singleton();
//        var_dump($this->identifyService->getIdentifyUser());exit;
        $params = [];
        $params["username"] = $username;
        $params["password"] = $password;
        $params["type"] = "account";
        $flag = $this->identifyService->login($params);
        if(!$flag){
            throw new AuthFailedException(STATUS_CODE_LOGIN_AUTH_FAILED);
        }
        return $this->identifyService->getIdentifyUser();
    }
    /**
     * 退出登录
     */
    public function logout()
    {
        $this->identifyService = IdentifyService::singleton();
        $this->log("退出成功",ExLog::DEBUG);
        #如果有必要 我们可以直接设置存储方式
        # $this->identifyService->setStorage(AuthFactory::createStorage("session"));
        return $this->identifyService->logout();
    }

    /**
     * 手机号码登录
     * @param $mobile
     * @param $vcode
     * @return mixed
     * @throws AuthFailedException
     * @throws \core\exception\CoreException
     */
    public function loginByMobile($mobile,$vcode){
        $this->identifyService = IdentifyService::singleton();
        $params = [];
        $params["mobile"] = $mobile;
        $params["vcode"] = $vcode;
        $params["type"] = "mobile";
        $flag = $this->identifyService->login($params);
        if(!$flag){
            throw new AuthFailedException(STATUS_CODE_LOGIN_AUTH_FAILED);
        }
        return $this->identifyService->getIdentifyUser();
    }

    /**
     * 检查手机号码是否存在
     */
    public function sendPassCode($mobile)
    {
        $params = [];
        $params["mobile"] = $mobile;
        #注意：登录验证 要求用户通过手机号码登录因此需要设置 type=mobile
        $params["type"] = "mobile";
        $result = $this->identifyService->checkParams(["mobile"=>$mobile]);
        if(!$result){
            throw new AuthFailedException(STATUS_CODE_LOGIN_AUTH_FAILED);
        }
        $helper = new HelperVerificationCode($mobile);
        return $helper->entry();
    }

    /**
     * 逻辑层验证用户登录状态
     */
    public function authenticate()
    {
        return $this->identifyService->authenticate();
    }
}