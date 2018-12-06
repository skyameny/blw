<?php
namespace api\model;

use core\model\BlModel;
use think\Model;
use core\utils\ExLog;
use core\model\SystemConfig;
use api\includes\helper\HelperApi;

/**
 * @desc api用户表
 * 
 * CREATE TABLE `aicall_api_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `enterprise_uid` int(11) NOT NULL DEFAULT '0' COMMENT '企业ID',
  `appid` varchar(100) NOT NULL DEFAULT '' COMMENT 'appid',
  `secret` varchar(100) NOT NULL DEFAULT '' COMMENT '密钥',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否可用1可用0不可用',
  `create_time` int(11) DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `index_appid` (`appid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
 *
 */

class ApiUser extends BlModel
{
    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 0;
    
    /**
     * 关联token
     * @return \think\model\relation\HasMany
     */
    public function access_token(){
        return $this->hasMany('api\\model\\AccessToken',"api_uid","id");
    }
    
    public function enterprise()
    {
        return $this->belongsTo("aicallup\\model\\Enterprise","eid","id");
    }
    
    /**
     * 创建token
     * 
     * @param unknown $ip
     */
    public function createAccessToken($ip=null)
    {
        $data = [];
        $data["ip"] = $ip;
        $data["access_token"] = HelperApi::createToken($this->getAttr("eid"),$this->getAttr("appid"));
        $token_expire_time = SystemConfig::getValue(CONFIGKEY_TOKEN_EXPIRE_TIME);
        $data['expiry_time'] = NOW_TIME + $token_expire_time;
        $data["create_time"] = NOW_TIME;
        $this->access_token()->save($data);
        ExLog::log("生成TOKEN:".$data["access_token"],ExLog::DEBUG);
        return ["access_token"=>$data["access_token"],"expires_in"=>$token_expire_time];
    }
    
    /**
     * 刷新token机制
     */
    public function refreshAccessToken($ip)
    {
        $token_expire_time = SystemConfig::getValue(CONFIGKEY_TOKEN_EXPIRE_TIME);
        $token = new AccessToken($this->getAttr("eid"),$this->getAttr("appid"));
        $this->access_token = $token->create();
        $this->expiry_time = NOW_TIME + $token_expire_time;
        $this->save();
        ExLog::log("刷新TOKEN:".$this->access_token,ExLog::DEBUG);
        return ["access_token"=>$this->access_token,"expires_in"=>$token_expire_time]; 
    }
    
    /**
     * 获取有效token
     * @return string
     */
    public function getAccessToken()
    {
        $returnValue = [];
        foreach ($this->access_token as $access_token){
            if($access_token->isUsedAble()){
                $returnValue[] = $access_token;
            }
        }
        return $returnValue;
    }
    
    /**
     * 设置状态
     * @param unknown $status
     */
    public function enable(){
        $data = ["status"=>self::STATUS_ENABLE];
        $this->save($data);
        ExLog::log("启用API用户",ExLog::INFO);
    }
    
    public function disable(){
        $data = ["status"=>self::STATUS_DISABLE];
        $this->save($data);
        ExLog::log("关闭API用户",ExLog::INFO);
    }
    
 
    
    
}