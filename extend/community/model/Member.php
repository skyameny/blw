<?php
/**
 * 用户模型
 * 
 * @author Dream<1015617245@qq.com>
 */
namespace community\model;

use core\model\BlModel;
use think\Config;

class Member extends BlModel
{
    //用户认证状态 
    const MEMBER_STATE_AUDITED = 1; //1已经认证
    const MEMBER_STATE_NOAUIT = 0;  //0未认证
    const MEMBER_STATE_REFUSED = 2; //0认证黑名单
    
    protected $name ;
    //获取状态
    public function getState()
    {
        return $this->getAttr("state");
    }
    
    //设置状态
    public function setState($state,$info="")
    {
        $this->setAttr("state", $state);
        //$this->setAttr("info", $info);
        return  $this->save();
    }
    /**
     * 获取基本信息
     */
    public function getBaseInfo()
    {
        $returnValue = $this->visible(Config::get("member_base_feilds"))->toArray();
        return $returnValue;
    }
    
    /**
     * 获取组信息
     */
    public function group()
    {
        return $this->hasOne("group_member","mid");
    }
    
    public function getGroup()
    {
        $gid = $this->group()->gid;
    }
    
    
    
}