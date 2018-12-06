<?php 
/**
 * 用户登录类
 */
namespace app\api\controller;

use core\controller\Api;
use core\controller\tool\EncryptionAttribute;
use core\service\ApiService;

class Passport extends Api
{
    use EncryptionAttribute;
    
    protected $validate = "app\\api\\validate\\ApiValidate";
    
    protected  $no_auth_action =["token"];

    /**
     * 验证登录信息
     */
    public function auth()
    {
        $requestParams = $this->request->param();
        $this->validate($this->request->param(), $this->validate);
        $api_service = ApiService::singleton();
        $user = $api_service->authUser($this->request->param("username"),$this->request->param("password"));
        if(!is_null($user)){
            $this->result("",STATUS_INVALID_APPID);
        }
        $token = $api_service->getToken($user);
        $this->result($token);
    }


    
    
}

