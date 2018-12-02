<?php
/**
 * 提供API公共方法
 * 
 */

namespace api\service;

use core\service\Service;
use api\dao\ApiUserDao;
use api\model\ApiUser;
use api\exception\ApiException;
use core\model\User;
use core\service\UserService;
use aicallup\model\Enterprise;

class ApiService extends Service
{
    private static $enterprise = null;
    /**
     * 获取token
     */
    public function getToken($user)
    {
        
    }
    
    /**
     * 
     * @return NULL
     */
    public function getEnterprise()
    {
        if(is_null(self::$enterprise)){
            self::$enterprise = null;
        }
        return self::$enterprise;
    }
    
    
    
    /**
     * 验证appid和secret
     * 
     * @param unknown $appid
     * @param unknown $secret
     */
    public function authUser($appid,$secret)
    {
        $dao = new ApiUserDao();
        $users = $dao->findByWhere(["appid"=>$appid,"secret"=>$secret,"status" => ApiUser::STATUS_ENABLE]);
        if(empty($users)){
            throw new ApiException(STATUS_INVALID_APPID);
        }
        return $users[0]; 
    }

    /**
     * 验证token
     * 
     * @param unknown $user
     * @param unknown $token
     * @return UserService
     */
    public function validateAccessToken($user, $token): User
    {
        
    }
    
    /**
     * 企业开启api初始化环境
     * @param Enterprise $enterprise
     */
    public function initEnv(Enterprise $enterprise)
    {
     //do nothing   
    }
    

}