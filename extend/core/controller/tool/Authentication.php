<?php
/**
 * 校验权限类
 */
namespace core\controller\tool;

use core\includes\session\SessionManagement;
use core\model\User as UserModel;
use think\Request;
use core\model\AuthRule;
use core\model\Role;

trait Authentication {
    /**
     * 校验
     */
    public function verification(UserModel $user = null)
    {
        if($this->notInAuthRule()){
            return true;
        }
        if(is_null($user)){
            $user = SessionManagement::getSession()->getUser()->getUserResource();
        }
        if(empty($user)){
            return false;
        }
        $role = $user->getOneRole();
        if(empty($role)){
            return false;
        }
        $request = Request::instance();
        $current_rule_name = $request->module()."/".$request->controller()."/".$request->action();
        if($role->isSysAdmin() || $role->hasRule($current_rule_name)){
            return true;
        }
        return false;
    }
    
    protected  function notInAuthRule()
    {
        $request = Request::instance();
        $current_rule_name = $request->module()."/".$request->controller()."/".$request->action();
        //$current_rule_name = "aicall/Enterprise/getEnterprise";
        $ruleArr = AuthRule::get(["name" => $current_rule_name]);
        return  is_null($ruleArr);
    }
    
    
}