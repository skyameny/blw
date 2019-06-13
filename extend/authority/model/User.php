<?php
/**
 * 用户
 */
namespace  authority\model;

use core\model\BlModel;

class User extends BlModel
{
    #内置超级管理员用户ID
    const SUPER_ADMIN_ID = 1;
    #管理员的type类型
    const ADMIN_USER_TYPE = 0;
    #可用状态
    const STATUS_ENABLE = 1;
    #不可用状态
    const STATUS_DISABLE = 0;

    protected $likeColumn = ["nickname"];

    public function roles()
    {
        return $this->belongsToMany('Role');
    }
    
    /**
     * 获取所属的角色
     * @return
     */
    public  function getRoles($status=null)
    {
        if(is_null($status)){
            $roles = $this->roles()->select();
        }else{
            $roles = $this->roles()->where(["status"=>Role::STATUS_ENABLE])->select();
        }
        if(empty($roles)){
            return $roles;
        }
        if(is_array($roles)){
            if (!empty($roles[0])){
                $singleRole = Role::get($roles[0]->getAttr('id'));
            }
            return $singleRole;
        }
        $role = null;
        if($this->id == self::SUPER_ADMIN_ID){
            $role = Role::get(self::SUPER_ADMIN_ID);
        }
        return $role;
    }
    
    public function getGardenId()
    {
        return $this->getAttr("gid");
    }
    
    public function isAdmin()
    {
        return ($this->getAttr("type") == 0);
    }

    #隐藏敏感信息
    public function toArray()
    {
        $this->hidden(["passwd","realname"]);
        return parent::toArray();
    }


}
