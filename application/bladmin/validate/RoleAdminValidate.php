<?php
/**
 * 用户模块  验证
 * User: keepwin100
 * Date: 2019-06-10
 * Time: 17:22
 */
namespace app\bladmin\validate;


use core\validate\BlValidate;

class RoleAdminValidate extends BlValidate
{
    protected $rule = array(
        "add_name"=>"require",
        "add_description"=>"require|isPassword",
    );
    protected $message = array(
        "add_name.require"=>"角色名称不能为空",
        "add_description.require"=>"角色描述不能为空",
    );

    protected $scene = array(
        #新建
        "addrole"    =>["add_name","add_description"],

    );
}