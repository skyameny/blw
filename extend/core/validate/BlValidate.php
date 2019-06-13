<?php
/**
 * 验证器
 * @author Dream<hukaijun@emicnet.com>
 */
namespace core\validate;

use authority\exception\PasswordException;
use authority\includes\helper\HelperPassword;
use think\Validate;

class BlValidate extends Validate
{
    protected $rule = [];
    
    protected $message = [];
    
    protected  $scene = [];

    /**
     * 域名正则
     * @param $value
     * @return bool
     */
    public static function isDomain($value)
    {
        $match='/^(http|https):[\/]{2}[a-zA-Z0-9]+[.]{1}[a-z\d\-]+[.]{1}[a-z\d]*[\/]*[A-Za-z\d]*[\/]*[A-Za-z\d]*/';
        return !!preg_match($match,$value);
    }

    /**
     * 手机号码正则
     * @param $value
     * @return bool
     */
    public static function isMobile($value){
        return !!preg_match('/^1[3456789]\d{9}$/',$value);
    }

    /**
     * 密码规则 不做正确错误校验
     * @param $value
     * @return bool
     */
    public static function isPassword($value)
    {
        try{
            return HelperPassword::validate($value);
        }catch (PasswordException $e){
            return false;
        }
    }

    /**
     * 用户名规则
     * @param $value
     * @return bool
     */
    public static function isUsername($value)
    {
        $match = "/^[A-Za-z0-9_]{3,20}+$/";
        return !!preg_match($match,$value);
    }
}