<?php
/**
 * 权限管理
 */
namespace app\admin\controller;

use core\controller\Admin;
use core\models\Role;
use core\utils\ExLog;
use core\exception\RoleException;
use core\service\UsersService;
use core\models\User;
use core\exception\UserException;
use core\service\AuthService;
use core\models\AuthAcess;
use core\models\AuthRule;

class Rbac extends  Admin
{
    /**
     * role列表
     * 
     */
    public function Role(){
        $keywords = trim($this->request->param("search"));
        if(!empty($keywords)){
            $role = Role::where('name','like','%'.$keywords."%");
        }else{
            $role = Role::where(1);
        }
        $roles = $role->paginate(10,false,[
            'type'     => 'Bootstrap4',
            'var_page' => 'page',
        ]);
        $this->assign("roles",$roles);
        return $this->fetch();
    }
    /**
     * addRole
     * models ajax
     * 
     */
    public function addRole()
    {
        if(!$this->request->isPost())
        {
          //  return false;
        }
        $this->checkParams("BlAdminValidate");
        $role_name = $this->request->param("role_name");
        $role_remark = $this->request->param("role_remark");
        $role_status = $this->request->param("status");
        if(strlen($role_name) >120){
            $this->result("",PARAM_TYPE_ERROR,"描述不符合要求");
        }
        $role_status = ($role_status>0)?1:0;
        $role = new Role();
        $role->name = $role_name;
        $role->remark = $role_remark;
        $role->status = $role_status;
        $role->create_time = time();
        $role->save();
        $this->log("添加角色");
        $this->result("添加成功");
    }
    /**
     * 编辑角色
     */
    public function editRole()
    {
        
    }
    
    /**
     * 删除角色
     */
    public function deleteRole()
    {
        $rid = $this->request->param("rid");
        if($rid == 1){
            $this->result("",ILLEGAL_OPRATION,"无法删除系统管理员");
        }
        if(empty($rid) ){
            $this->result("",PARAM_ERROR,"需要指定角色ID");
        }
        $role = Role::get($rid);
        if(is_null($role)){
            $this->result("",PARAM_ERROR,"该角色不存在");
        }
        //删除DB
        try {
            UsersService::singleton()->removeRole($role);
        } catch (RoleException $e) {
            ExLog::log("Role remove failed:".$e->getMessage());
            $this->result("",ROLE_REMOVE_FAILED,"删除失败");
        }
        $this->result("删除成功");
    }
    /**
     * ***************
     * 用户管理
     * 
     * 
     * ****************
     */
    public function User()
    {
        $keywords = trim($this->request->param("search"));
        if(!empty($keywords)){
            $user = User::where('nickname|username|mobile|telphone','like','%'.$keywords."%");
        }else{
            $user = User::where(1);
        }
        $users = $user->paginate(10,false,[
            'type'     => 'Bootstrap4',
            'var_page' => 'page',
        ]);
        $roles = Role::all(["status"=>1]);
        $this->assign("users",$users);
        $this->assign("roles",$roles);
        return $this->fetch();
    }
    
    /**
     * 创建用户
     */
    public function addUser()
    {
        if(!$this->request->isPost())
        {
            //  return false;
        }
        $this->checkParams("BlAdminValidate");
        $rquestData = $this->request->param();
        $role  = Role::get($this->request->param("user_role"));
        try {
            $result = UsersService::singleton()->addUser($rquestData,$role);
        } catch (UserException $e) {
            ExLog::log("添加用户失败：".$e->getMessage(),ExLog::DEBUG);
            $this->result("",$e->getCode(),$e->getMessage());
        }
        $this->log("添加用户");
        $this->result("添加成功");
    }
    
    
    public function deleteUser()
    {
        $uid = $this->request->param("uid");
        if($uid == 1){
            $this->result("",ILLEGAL_OPRATION,"无法删除系统管理员");
        }
        if(empty($uid) ){
            $this->result("",PARAM_ERROR,"需要指定用户ID");
        }
        $user = User::get($uid);
        if(is_null($user)){
            $this->result("",PARAM_ERROR,"该用户不存在");
        }
        //删除DB
        try {
            UsersService::singleton()->removeUser($user);
        } catch (RoleException $e) {
            ExLog::log("Role remove failed:".$e->getMessage());
            $this->result("",ROLE_REMOVE_FAILED,"删除失败");
        }
        $this->result("删除成功");
        
    }
    
    
    
    
    
    /**
     * 权限管理
     * 添加应用权限
     */
    public function  authorize()
    {
        $discribe = [
            "admin/rbac"=>"配置系统用户的行为，建议只有超级管理员拥有。",
            "admin/system"=>"配置系统的各种行为，建议只有超级管理员拥有。",
            "admin/garden"=>"控制社区运行的行为，可以管理社区的一切行为。",
            "admin/operate"=>"控制网站运行的行为，可以管理系统运营的一切行为。",
            "admin/test"=>"控制社区运行的行为，可以管理社区的一切行为。",
        ];
        $menus = AuthRule::getAllAuth();
        foreach ($menus as $key => $menu){
            if(array_key_exists($menu["name"], $discribe)){
                $menus[$key]["discribe"] = $discribe[$menu["name"]];
            }
        }
        $this->assign("auth_rules",$menus);
        //var_dump($menus);exit;
        
        return  $this->fetch();
    }
    
    /**
     * 授权信息
     */
    public function authorizeList()
    {
        $role_id = $this->request->param("rid");
        $role = Role::get($role_id);
        if(is_null($role)){
            $this->result("",ROLE_NOT_EXSIT,"角色不存在");
        }
        $auth_access = AuthService::getAuthRule();
        $role_access = AuthService::getAuthRule($role);
        return $this->fetch();
    }
    
    
    
}