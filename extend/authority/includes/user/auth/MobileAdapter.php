<?php
/**
 * 手机号码登录
 * User: keepwin100
 * Date: 2019-06-05
 * Time: 23:20
 */
namespace authority\includes\user\auth;

use authority\exception\AuthFailedException;

class MobileAdapter extends CommonAdapter
{
    protected $mobile;

    protected $vcode;

    public function login(array $params)
    {
        if(!isset($params["mobile"]) || empty($params["mobile"])){
            throw new AuthFailedException(STATUS_CODE_PARAM_ERROR);
        }

        if(!isset($params["vcode"]) || empty($params["vcode"])){
            throw new AuthFailedException(STATUS_CODE_PARAM_ERROR);
        }
        $this->mobile = $params['mobile'];
        $this->vcode = $params['vcode'];
        $identifyUser = $this->verification();
        self::getStorage()->startStorage($identifyUser);
        return true;
    }
    public function checkParams(array $params)
    {
        // TODO: Implement checkParams() method.
    }

    public function verification()
    {
        //
    }
}

