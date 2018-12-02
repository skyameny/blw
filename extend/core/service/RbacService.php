<?php
namespace  core\service;
use core\model\AuthRule;
use core\includes\session\SessionManagement;
use core\includes\user\User;
use core\model\Role;
use core\exception\CommonException;
use think\migration\command\migrate\Status;

/**
 * 权限控制服务
 */

class RbacService extends Service
{
    /**
     * 搜索角色
     * 
     * @param unknown $condition
     * @return boolean|\think\static[]|\think\false
     */
    public function  searchRoles($condition)
    {
        $role_model = new Role();
        $ispaginate = !empty($condition["page"]);
        return $role_model->searchInstances($condition,["name|remark"],$ispaginate);
    }
    
    /**
     * 
     * @param unknown $role_id
     * @return \core\model\Role|NULL
     */
    public function getRole($role_id)
    {
        return  Role::get($role_id);    
    }
    
    /**
     * 获取rule列表
     * 
     * @param array $role
     */
    public function getAuthRule($role=[])
    {
        if(!is_null($role)){
            $rules = $role->authRule;
        }else{
            $rr_model = new AuthRule();
            $rules = $rr_model->searchInstances();
        }
        return empty($rules)?false:$this->getRuleSonIds($rules, 0);
    }
    
    /**
     * 递归树
     * 
     * @param unknown $rules
     * @param unknown $pid
     * @return unknown[][]
     */
    private function getRuleSonIds(&$rules,$pid)
    {
        $returnValue = [];
        foreach ($rules as $key=> $rule){
            if($rule->getAttr("parent_id") == $pid){
                $ruleInfo= $rule->toArray();
                unset($rules[$key]);
                $sons = $this->getRuleSonIds($rules, $rule->getAttr("id"));
                if(!empty($sons)){
                    $ruleInfo["children"] = $sons;
                }
                //去掉中间表
                if(isset($ruleInfo["pivot"])){
                    unset($ruleInfo["pivot"]);
                }
                $returnValue[] = $ruleInfo;
            }
        }
        return $returnValue;
    }
    
    /**
     * 添加角色
     * @param unknown $param
     * @return number|\think\false
     */
    public function addRole($param)
    {
        $role = new Role();
        if(Role::get(["name"=>$param['role_name']])){
            throw  new CommonException("",STATUS_CODE_ROLE_NAME_EXISTS);
        }
        $data = [];
        $data["name"] = $param['role_name'];
        $data["remark"] = isset($param['role_remark']) ?$param['role_remark']: "";
        $data["status"] = isset($param['role_status']) ? $param['role_status']: Role::STATUS_ENABLE;
        $data["type"] = Role::TYPE_CUSTOM; // 默认自定义
        $data["create_time"] = NOW_TIME;
        $result = $role->save($data);
        if(!$result){
            throw  new CommonException("",STATUS_CODE_ADD_ROLE_FAILED);
        }
        return $role;
    }
    
    public function editRole($param)
    {
        $role = Role::get($param["id"]);
        if(!$role)
        {
            throw  new CommonException("",STATUS_CODE_ROLE_NOT_EXISTS);
        }
        if($role->getAttr("type") ==Role::TYPE_DEFAULT){
            throw  new CommonException("",STATUS_CODE_ROLE_EDIT_DISABLE);
        }
    }
    
    
    
    
    /**
     * 验证控制的权限
     * @param unknown $action
     * @param unknown $role
     * @return boolean
     */
    public static function checkAuth($action,$role=null)
    {
        $returnValue = false;
        if(is_null($role)){
            $roles = SessionManagement::getSession()->getUserRoles();
        }
        
        return $returnValue;
    }
    
    
    /**
     * 获取权限菜单
     * 
     */
    public static function getMenu(User $user)
    {
        $user_model = \core\models\User::get($user->getIdentifier());
        if(empty($user_model)){
            return false;
        }
        
    }
    
    
    
}
