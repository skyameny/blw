<?php
/**
 * 用户token表
 * User: keepwin100
 * Date: 2019-06-12
 * Time: 17:17
 */

namespace community\model;
use core\model\BlModel;

class MemberToken extends  BlModel
{
    #token生命时间
    const TOKEN_LIFE_TIME = 86400;

    public function is_expired()
    {
        return NOW_TIME >= $this->getAttr("expiry_time");
    }

    /**
     * 更新token时间
     */
    public function refresh()
    {
        return $this->isUpdate(true)->save([
            "expiry_time"=>NOW_TIME + self::TOKEN_LIFE_TIME,
            "create_time"=>NOW_TIME
            ]);
    }

    /**
     * 是否匹配
     * @param $token
     * @return bool
     */
    public function isMatch($token)
    {
        return $token === $this->getAttr("access_token");
    }

    /**
     * 生产唯一字符串
     * @return string
     */
    public function buildToken()
    {
        return md5(uniqid(time()));
    }
}