<?php

namespace authority\dao;


use core\dao\Dao;
use core\exception\UserException;
use core\model\Role;
use core\model\User;
use core\model\UserRole;

class UserDao extends Dao
{
    protected $class = User::class;

    protected $defaultSort = ['order', 'create_time desc'];

    protected $userVisible = ['id','username','status','mobile','create_time'];

    /**
     * 账户分页
     */
    public function getUsersSearchPageByEidAndName($eid,$name,$paginationParams){
        $returnValue = [];
        $where['eid'] = $eid;
        $where['username'] = ['like', "%$name%"];

        $fields = $this->userVisible;

        $returnValue["count"] = $this->count($where, $paginationParams);
        $returnValue['users'] = $this->findByWhere($where, $paginationParams, $fields);
        foreach($returnValue['users'] as $key => $val) {
            $returnValue['users'][$key] = $this->formatUserDetail($val);
        }
        return $returnValue;
    }
    public function getUsersPageByEid($eid,$paginationParams){
        $returnValue = [];
        $where['eid'] = $eid;

        $fields = $this->userVisible;

        $returnValue["count"] = $this->count($where, $paginationParams);
        $returnValue['users'] = $this->findByWhere($where, $paginationParams, $fields);
        foreach($returnValue['users'] as $key => $val) {
            $returnValue['users'][$key] = $this->formatUserDetail($val);
        }
        return $returnValue;
    }


    public function formatUserDetail($value){
        $userId = $value['id'];
        $value['roles'] = $this->getRolesByUids($userId);
        $value['is_ep_admin'] = $value->is_ep_admin;
        return $value;
    }

    public function getRolesByUids($userId){
        $where['user_id'] = $userId;
        $role_ids =  UserRole::where($where)->column('role_id');
        $roles = Role::all($role_ids);
        foreach ($roles as &$role){
            $role->visible(['id','name'])->toArray();
        }
        return $roles;
    }

}