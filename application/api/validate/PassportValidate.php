<?php
/**
 * Created by PhpStorm.
 * User: keepwin100
 * Date: 2019-06-12
 * Time: 14:45
 */

namespace app\api\validate;

use core\validate\BlValidate;

class PassportValidate extends  BlValidate
{
    protected $rule = array(
        "app_username"=>"require",
        "app_password"=>"require",
    );

    protected $message = array(
        "app_username.require"=>"用户名不能为空",
        "app_password.require"=>"密码不能为空",
    );

    protected $scene = array(
        "login"    =>["app_username","app_password"],
    );

}