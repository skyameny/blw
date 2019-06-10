<?php

namespace authority\dao;

use authority\Model\AuthRule;
use core\dao\Dao;
use core\exception\AuthFailedException;
use core\exception\RoleException;
use core\model\AuthAccess;
use core\model\Role;
use core\model\User;
use think\Db;

class RoleDao extends Dao
{
    protected $class = Role::class;

    protected $defaultSort = ['type', 'create_time desc'];

    protected $roleVisible = ['id','name','status','create_time','remark'];

    /**
     * 角色分页
     */
    public function getRolesPageByEid($eid,$paginationParams){
        $returnValue = [];
        $where = "id=".User::DEFAULT_EP_ROLEID. " or eid=".$eid;
        $fields = $this->roleVisible;
        $returnValue["count"] = $this->count($where, $paginationParams);
        $returnValue['roles'] = $this->findByWhere($where, $paginationParams,$fields);
        foreach($returnValue['roles'] as $key => $val) {
            $returnValue['roles'][$key] = $this->formatRoleDetail($val);
        }
        return $returnValue;
    }

    public function getRolesSearchPageByEidAndName($eid,$name,$paginationParams){
        $returnValue = [];
        $where = "(id=".User::DEFAULT_EP_ROLEID. " or eid=".$eid. ") and name like '%".$name."%'";
        $fields = $this->roleVisible;
        $returnValue["count"] = $this->count($where, $paginationParams);
        $returnValue['roles'] = $this->findByWhere($where, $paginationParams,$fields);
        foreach($returnValue['roles'] as $key => $val) {
            $returnValue['roles'][$key] = $this->formatRoleDetail($val);
        }
        return $returnValue;
    }

    /**
     * 获取角色详情
     */
    public function getRoleById($id){
        $role = $this->findById($id)->visible($this->roleVisible)->toArray();
        $role = $this->formatRoleDetail($role);
        return $role;
    }

    public function formatRoleDetail($value){
        $roleId = $value['id'];
        $rules = $this->getAuthRulesByIds($roleId);
        $value['rules'] = $rules;
        // todo: IS EP ADMIN
        return $value;
    }

    public function getAuthRulesByIds($roleId){
        $where['role_id'] = $roleId;
        $auth_rule_ids =  AuthAccess::where($where)->column('auth_rule_id');
        $auth_rules = AuthRule::all($auth_rule_ids);
        foreach ($auth_rules as &$rule){
            $rule->visible(['id','title','name','parent_id'])->toArray();
        }
        return $auth_rules;
    }

}