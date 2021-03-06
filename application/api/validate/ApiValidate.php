<?php
/**
 * API接口验证器
 * @author Dream<hukaijun@emicnet.com>
 */
namespace app\api\validate;

use think\Validate;
use core\includes\helper\HelperPassword;
use core\exception\PasswordException;

class ApiValidate extends Validate
{
    protected $rule = array(
        "appid"=>'require|regex:^[0-9a-zA-Z\_]{20}$', 
        "secret"=>'require|regex:^[0-9a-zA-Z\_]{16}$',
        "grant_type"=>'require|eq:api',
    ); 
     
    protected $message = array(
        "appid.require"=>STATUS_INVALID_APPID,
        "appid.regex"=>STATUS_INVALID_APPID,
        "secret.require"=>STATUS_INVALID_SECRET,
        "secret.regex"=>"SECRET参数格式错误",
        "grant_type.require"=>STATUS_INVALID_TYPE,
        "grant_type.eq"=>STATUS_INVALID_TYPE,
    );
    
    protected  $scene = array(
        //获取token
        'token' => ["appid","secret","grant_type"],
        //startTask
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
}