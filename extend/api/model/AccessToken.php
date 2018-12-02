<?php
namespace api\model;

/**
 * API用户令牌表
 *
 * Table Name aicall_access_token
 *
 * CREATE TABLE `aicall_access_token` (
 * `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 * `api_uid` int(11) NOT NULL COMMENT 'uid',
 * `access_token` varchar(100) NOT NULL DEFAULT '' COMMENT '令牌',
 * `create_time` int(11) NOT NULL COMMENT '创建时间',
 * `expiry_time` int(11) NOT NULL COMMENT '过期时间',
 * PRIMARY KEY (`id`),
 * KEY `index_api_uid` (`api_uid`)
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 */
use core\model\SystemConfig;
use think\Model;
use core\model\BlModel;

class AccessToken extends BlModel
{

    const TOKEN_STATUS_EXPIRED = 0;
 // 已经过期
    const TOKEN_STATUS_RESERVED = 1;
 // 保留使用
    const TOKEN_STATUS_NORMAL = 2;
 // 正常
    protected $token_default_expire_time;
 // token持续时间
    protected $token_duration_time;
 // token间隔时间
//     protected $autoWriteTimestamp = true;

    protected $createTime = 'create_time';

    public function __construct($data = [])
    {
        parent::__construct($data);
        $this->token_default_expire_time = SystemConfig::getValue("token_expire_time") ?? 7200;
        $this->token_duration_time = SystemConfig::getValue("token_duration_time") ?? 300;
    }

    /**
     * 插入后自动清理
     */
    private function after_insert()
    {
        // do nothing
    }

    /**
     * 是否可用
     */
    public function isUsedAble()
    {
        return ($this->status() > 0);
    }

    /**
     * 获取token状态
     *
     * 规定:
     * 有效期内属于"正常"
     * 有效期外贪睡时间内"保留"
     * 超过贪睡时间任务是"过期"
     *
     * @return string
     */
    public function status()
    {
        $expiry_time = $this->getAttr("expiry_time");
        if ($expiry_time > NOW_TIME) {
            return self::TOKEN_STATUS_NORMAL; 
        } elseif ($expiry_time >  NOW_TIME 
            && $expiry_time <  NOW_TIME + $this->token_duration_time) {
            return self::TOKEN_STATUS_RESERVED;
        } else {
            return self::TOKEN_STATUS_EXPIRED;
        }
    }

    /**
     * 刷新token
     */
    public function refresh()
    {
        // 插入类型无法自己刷新 请调用service中的refresh方法;
    }
}