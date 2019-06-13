<?php
/**
 * 用户模块  验证
 * User: keepwin100
 * Date: 2019-06-10
 * Time: 17:22
 */
namespace app\bladmin\validate;


use core\validate\BlValidate;

class UserAdminValidate extends BlValidate
{
    protected $rule = array(
        "register_username"=>"require|isUsername",
        "register_password"=>"require|isPassword",
        "register_mobile"=>"require|isMobile",
        "roles"         =>"require",
        "uid"           =>"require"
    );
    protected $message = array(
        "register_username.require"=>"用户名不能为空",
        "register_username.isUsername"=>"用户名不符合规范",
        "register_password.require"=>"密码不能为空",
        "register_password.isPassword"=>"密码必须同时包含大小写、数字和特殊字符",
        "roles.require"=>"必须要为用户指明角色",
        "uid.require"   =>"用户ID不能为空",
    );

    protected $scene = array(
        #注册
        "adduser"    =>["register_username","register_password","roles"],
        "modifyuser" =>["uid"],

    );
}