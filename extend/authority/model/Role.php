<?php
/**
 * 角色模型
 *
 * @author Dream<1015617245@qq.com>
 *
 */
namespace authority\model;

use core\model\BlModel;
use think\Db;
use core\exception\RoleException;
use core\utils\ExLog;

class Role extends BlModel
{
    const STATUS_ENABLE =1;
    const STATUS_DISABLE = 0;
    const EP_ADMIN_ID = 2;
    
    /**
     * 关联的 权限规则
     * @return \think\model\relation\BelongsToMany
     */
    public function authRule()
    {
        return $this->belongsToMany("auth_rule","auth_access","auth_rule_id");
    }

    public function isEqual(BlModel $role)
    {
        if(!$role instanceof Role){
            return false;
        }
        if($role->getAttr("id") === $this->getAttr("id"))
        {
            return true;
        }
        return false;
    }

    /**
     * 是否是系统管理员
     * @return boolean
     */
    public function isSysAdmin()
    {
        return ($this->getAttr("id") == 1);
    }
    
    /**
     * 角色名称
     * @return string
     */
    public function getLable()
    {
        return $this->getAttr("name");
    }
    
    /**
     * 删除角色
     */
    public function remove()
    {
        $roleNum =Db::name("RoleUser")->where(["role_id"=>$this->id])->count();
        if($roleNum>1){
            throw new RoleException(USER_ROLES_CAN_NOT_DEL);
        }
        if($this->id ==1){
            throw new RoleException(SYS_ADMIN_CAN_NOT_DEL);
        }
        
        $this->authRule()->detach(["role_id"=>$this->id]);
        ExLog::log("删除角色关联：".$this->db()->getLastSql(),ExLog::DEBUG);
        
        if(!$this->delete()){
            throw new RoleException(DEL_THIS_ROLE_FAILED);
        }
        ExLog::log("正在删除角色:".$this->getAttr("name"));
        return true;
    }
    /**
     * 是否觉有权限
     * @param unknown $rule
     */
    public function hasRule($rule)
    {
        return !!$this->authRule()->where(["role_id"=>$this->id,"name" => $rule])->select();
    }
    
    /**
     * 获取用户权限的菜单
     */
    public function getAuthMenus($force =false)
    {
        $menus = [];
        if($this->isSysAdmin()){
            return $this->getAllMenus();
        }
        $cacheKey = "MENU_SF87DD1ED4S212_".$this->getAttr("id");
        cache($cacheKey,null);
        if(!cache($cacheKey) || $force )
        {
            $authRules = $this->authRule();
            $aus = $authRules->where(["role_id"=>$this->id,"status"=>AuthRule::STATUS_USEDABLED,"is_show"=>1,"parent_id"=>0])->order("order desc")->select();
            
            foreach ($aus as $au){
                $menus[$au->getAttr("id")] = $au->visible(["title","name","order","is_show","nav_icon","id"])->toArray();
                $chidren = $au->getSonRules(1);
                if(empty($chidren)){
                    continue;
                }
                foreach ($chidren as $cru){
                    $menus[$au->getAttr("id")]["children"][] = $cru->visible(["title","name","order","is_show","nav_icon","id"])->toArray();;
                }
            }
            if(empty($menus)){
                return false;
            }
            cache($cacheKey,$menus);
        }
        
        return cache($cacheKey);
    }
    
    /**
     * 获取所有的菜单
     * 超级管理员拥有一切权限
     */
    public function getAllMenus($force = false)
    {
        $cacheKey = "MENU_SF87DD1ED4S21_ADMIN";
        cache($cacheKey,null);
        $menus = [];
        if(!cache($cacheKey) || $force ){
            $rules = AuthRule::all(function($query){
                $query->where(["status"=>AuthRule::STATUS_USEDABLED,"is_show"=>1,"parent_id"=>0])->order('order', 'asc');
            });
                foreach ($rules as $au){
                    $menus[$au->getAttr("id")] = $au->visible(["title","name","order","is_show","nav_icon","id"])->toArray();
                    $chidren = $au->getSonRules(1);
                    if(empty($chidren)){
                        continue;
                    }
                    foreach ($chidren as $cru){
                        $menus[$au->getAttr("id")]["children"][] = $cru->visible(["title","name","order","is_show","nav_icon","id"])->toArray();;
                    }
                }
                if(empty($menus)){
                    return false;
                }
                cache($cacheKey,$menus);
        }
        
        return cache($cacheKey);
    }
    
    
}




