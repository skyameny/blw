<?php
/**
 * 用户模型
 * 
 * @author Dream<1015617245@qq.com>
 */
namespace community\model;

use core\model\BlModel;
use think\Config;
use think\Request;

class Member extends BlModel
{
    //用户认证状态 
    const MEMBER_STATE_AUDITED = 1; //1已经认证
    const MEMBER_STATE_NOAUIT = 0;  //0未认证
    const MEMBER_STATE_REFUSED = 2; //0认证黑名单

    protected $likeColumn = ["nickname"];

//    public function tokens(){
//        $this->hasMany('MemberToken');
//    }

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

    public function getGardenId()
    {
        return $this->getAttr("cid");
    }

    /**
     * 创建TOKEN
     * @param null $devices
     * @param null $ua
     * @return string
     * @throws \think\exception\DbException
     */
    public function createToken($devices=null,$ua=null)
    {
        $token = MemberToken::get(["mid" => $this->getAttr("id")]);
        if (!empty($token)) {
            $token->delete();
        }
        #保存
        $member_token = new MemberToken();
        $saveData = [];
        $saveData["mid"] = $this->id;
        $saveData["access_token"] = $member_token->buildToken();
        $saveData["create_time"] = NOW_TIME;
        $saveData["ip"] = Request::instance()->ip();
        $saveData["devices"] = is_null($devices)?"":strval($devices);
        $saveData["use_agent"] = is_null($ua)?"":strval($ua);
        $saveData["expiry_time"] = NOW_TIME + MemberToken::TOKEN_LIFE_TIME;
        $member_token->save($saveData);
        return $saveData["access_token"];
    }

    /**
     * 获取token
     * @return bool|MemberToken
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getToken()
    {
        $member_token = new MemberToken();
        $token = $member_token->where(["mid" => $this->id])->select();
        if(empty($token)){
            return false;
        }
        return current($token);
    }
    
    
    
}