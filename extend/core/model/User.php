<?php
/**
 * 用户
 */
namespace  core\model;

use core\model\Role;


class User extends BlModel
{
    const SUPER_ADMIN_ID = 1; //超级管理员用户ID
    
    const ADMIN_USER_TYPE = 0;
    
    
    //protected  $table = 'user';
    
    public function roles()
    {
        return $this->belongsToMany('Role');
    }
    
    /**
     * 获取所属的角色
     * @return \think\model\relation\BelongsToMany|Role
     */
    public  function getOneRole()
    {
        $roles = $this->roles()->where(["status"=>Role::STATUS_ENABLE])->select();
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
    
    
}
