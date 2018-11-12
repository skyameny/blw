<?php
/**
 * API接口验证器
 * @author Dream<hukaijun@emicnet.com>
 */
namespace app\account\validate;

use think\Validate;
use core\includes\helper\HelperPassword;
use core\exception\PasswordException;

class BlValidate extends Validate
{
    protected $rule = array(
        'username'           => 'require|isUsername',
        'passwd'             => 'require',
        "password"           => 'require|isPasswd',
        'captcha|验证码'      =>'require|captcha',
        
        'role_name'=>'require',
    );
    
    protected $message = array(
        'username.require'     => "用户名不能为空",
        'username.isUsername'  => "用户名不符合要求",
        'password.isPasswd'   => "密码格式不正确",
        'password.require'    => "密码不能为空",
        'passwd.require'      => "密码不能为空",
        'role_name.require'       =>  "角色名称不能为空",
        
        
    );
    
    protected  $scene = array(
        'getCommunity' => ["passwd","username"],
    
    );
    
    protected function isDomain($value)
    {
        $match='/^(http|https):[\/]{2}[a-zA-Z0-9]+[.]{1}[a-z\d\-]+[.]{1}[a-z\d]*[\/]*[A-Za-z\d]*[\/]*[A-Za-z\d]*/';
        return !!preg_match($match,$value);
    }

    protected function isJson($value)
    {
        return !!(json_decode($value));
    }
    
    /**
     * 用户名规则 数字字母_ 3-20位
     */
    protected function isUsername($value)
    {
        $match = "/^[A-Za-z0-9_]{3,20}+$/";
        return !!preg_match($match,$value);
    }
    
    protected function isPasswd($value)
    {
        try {
            HelperPassword::validate($value);
        } catch (PasswordException $e) {
            return false;
        }
        return  true;
    }
    
    protected function isToken($value) 
    {
        $enterprise = Enterprise::get(["token"=>$value]);
        return !is_null($enterprise);
    }
}