<?php
/**
 * 控制权限模型
 */
namespace authority\model;

use core\model\BlModel;

class AuthRule extends BlModel
{
    const STATUS_DISABLED = 0;

    const STATUS_ENABLED = 1;

    /**
     * 与actions 多关联
     * @return \think\model\relation\BelongsToMany
     */
    public function authActions()
    {
        return $this->belongsToMany("auth_action","auth_rule_action","rule_id");
    }
    /**
     * 是否属于菜单
     * 
     * @return boolean
     */
    public function isMenu()
    {
        return ! ! $this->getAttr("is_show");
    }
    
    /**
     * 是否属于顶级的规则
     * parent_id !=0
     */
    public function isTopRule()
    {
        return !!$this->getAttr("parent_id");
    }
    
    /**
     * 获取所有的权限列表
     * 包含关联关系
     */
    public static function getAuthTree()
    {
        $returnValue = [];
        $topAuths = self::all(function($query){
            $query->where(["parent_id"=>0,"status"=>self::STATUS_USEDABLED])->order('order', 'asc');
        });
        if(is_null($topAuths)){
            return $returnValue;
        }
        foreach ($topAuths as $key=>$topAuth)
        {
            $returnValue[$key] = $topAuth->toArray();
            $returnValue[$key]["children"] = $topAuth->getSonAuths();
        }
        
        return $returnValue;
    }
    
    /**
     * 获取子类分类
     * 防止性能萎缩  这里只做3层结构
     * @param number $level
     */
    public function getSonAuths($level = 0)
    {
        $returnValue = [];
        $auth_id = $this->getAttr("id");
        $auth_rules = self::all(function($query) use ($auth_id){
            $query->where(["parent_id"=>$auth_id,"status"=>self::STATUS_USEDABLED])->order('order', 'asc');
        });
        if(is_null($auth_rules)){
            return [];
        }
        if($level ==1){
            $ars = []; 
            foreach ($auth_rules as $k=>$ar){
                $ars[$k] = $ar->toArray();
            }
            return $ars;
        }
        foreach ($auth_rules as $key =>$auth_rule){
            $returnValue[$key] = $auth_rule->toArray();
            $returnValue[$key]["children"]= $auth_rule->getSonAuths(1);
        }
        return $returnValue;
    }

    /**
     * 判断规则是否可用
     * 
     * @return boolean
     */
    public function Usedable()
    {
        return $this->getAttr("status") == self::STATUS_USEDABLED;
    }

    /**
     * 获取规则名称
     */
    public function getName()
    {
        return $this->getAttr("name");
    }

    /**
     * 为指定角色绑定控制
     * 
     * @param Role $role            
     */
    public function bindRole(Role $role)
    {
        $SonRules = $this->getSonRules(1);
        foreach ($SonRules as $srule) {
            $srule->bindRole($role);
        }
        // 判断是否已经绑定
        if (! $role->hasRule($this)) {
            $authAccess = new AuthAcess();
            $authAccess->role_id = $role->id;
            $authAccess->rule_name = $this->name;
            $authAccess->type = $this->type;
            $authAccess->save();
        };
    }

    /**
     * 获取子操作
     * $deep 最多两层
     *
     * @param number $deep            
     */
    public function getSonRules($deep = 0)
    {
        $returnValue = [];
        $rules = AuthRule::all([
            "parent_id" => $this->getAttr("id")
        ]);
        if (is_null($rules)) {
            return $rules;
        }
        if ($deep == 1) {
            return $rules;
        } else {
            $ssrules = [];
            foreach ($rules as $rule) {
                $ssrules = $rule->getSonRules();
                $returnValue = array_merge($returnValue, $ssrules);
            }
            $returnValue = array_merge($returnValue, $rules);
        }
        return $returnValue;
    }
    
}