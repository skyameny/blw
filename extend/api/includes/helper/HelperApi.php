<?php
namespace  api\includes\helper;

use core\includes\helper\HelperRandom;

class HelperApi
{
    /**
     * 创建token
     * 
     * 这里简单的md5
     */
    public static function createToken($enterprise_uid,$appid)
    {
        $salt = HelperRandom::generateString(32);
        return md5($appid.$enterprise_uid.$salt);
    }
    
    /**
     * 生成API
     * appid
     * 
     * @param unknown $enterprise_uid
     * @return string
     */
    public static function createAppid($enterprise_uid)
    {
        $salt = HelperRandom::generateString(32);
        return md5($salt.$enterprise_uid);
    }
    /**
     * 生成密钥
     * @return string
     */
    public static function createSecret()
    {
        $salt = HelperRandom::generateString(32);
        return substr($salt, 0,15).md5($salt).substr($salt, 0,16);
    }
    
    
}