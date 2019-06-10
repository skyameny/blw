<?php
/**
 * 获取手机验证码 工具类
 * User: Dream<hukaijun@emicnet.com>
 * Date: 2019-05-15
 * Time: 18:47
 */

namespace core\utils\helper;

use core\exception\CoreException;
use core\includes\helper\HelperRandom;
use core\utils\ExLog;
use think\Cache;

class HelperVerificationCode
{
    /**
     * 验证码缓存前缀
     */
    const CODE_CACHE_PREFIX = "vcode_cache_";

    #限制每日10条
    const MAX_SEND_LIMIT_DAILY = 10;

    #10分钟有效期
    const EXPIRATION_TIME= 600;

    #验证码长度
    const VCODE_LENGTH = 4;

    #请求时间间隔
    const REQUEST_DURATION_TIME_LIMIT=60;

    protected $mobile = "";

    protected $code = "";

    protected $last_time = 0;

    private  $cache_date = [
        "last_time"=>0,
        "count"=>0,
        "mobile"=>"",
        "code"=>""
    ];


    public function __construct($mobile)
    {
        $this->mobile = $mobile;
    }

    /**
     * 验证手机号
     * @param $verification_code
     * @return bool
     */
    public function verify($verification_code)
    {
        $code_data = Cache::get(self::CODE_CACHE_PREFIX.$this->mobile);
        if(is_null($code_data)){
           return false;
        }
        if(isset($code_data["last_time"])
            && isset($code_data["code"])
            && (NOW_TIME-$code_data["last_time"]<=self::EXPIRATION_TIME)
            && $code_data["code"] == $verification_code){
            #只能验证一次 清除缓存
            Cache::set(self::CODE_CACHE_PREFIX.$this->mobile,null);
            return true;
        }
        return false;
    }

    /**
     * 创建验证码
     * @return bool
     *
     * @throws CoreException
     */
    public function entry()
    {
        $old_data = Cache::get(self::CODE_CACHE_PREFIX.$this->mobile);
        if(!is_null($old_data)){
            if(NOW_TIME - $old_data["last_time"] < self::REQUEST_DURATION_TIME_LIMIT){
                throw new CoreException(STATUS_CODE_TOO_FREQUENT_OPERATION);
            }
            if($old_data["last_time"] > mktime(0,0,0,
                    date("m"),date("d"),date("Y"))){
                if($old_data["count"]>=self::MAX_SEND_LIMIT_DAILY){
                    throw new CoreException(STATUS_CODE_OVER_MAX_LIMIT_SEND_CODE);
                }
            }
        }

        $this->code = HelperRandom::generateNumber(self::VCODE_LENGTH);

        /**
         * 发送短信 先看是不是联通本地化
         */
        //$result = $smsSendHistoryService->sendSmsByData($enterprise, $content, $this->mobile);
//        if($result["send_status"] !== 0)
//        {
//            ExLog::log("短信发送失败：".$result["message"],ExLog::ERROR);
//            throw new CoreException(STATUS_CODE_SEND_VCODE_FAILED);
//        }

        #发送短信设置缓存
        $this->cache_date["last_time"] = NOW_TIME;
        $this->cache_date["count"] = $old_data["count"] + 1;
        $this->cache_date["mobile"] = $this->mobile;
        $this->cache_date["code"] = $this->code;

        ExLog::log("短信发送成功：".json_encode($this->cache_date),ExLog::ERROR);
        Cache::set(self::CODE_CACHE_PREFIX.$this->mobile,$this->cache_date);
        return true;
    }
}

