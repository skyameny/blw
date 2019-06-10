<?php
 namespace authority\controller;
 use authority\model\AuthAction;
 use authority\model\AuthRule;
 use authority\service\EpAuthorityService;
 use authority\utils\CacheActions;
 use authority\validate\AuthorityValidate;
 use core\controller\Frame;
 use core\exception\CoreException;
 use core\includes\session\SessionManagement;
 use core\service\UserService;
 use core\controller\tool\ApiPagination;
 use think\Db;


 abstract  class AbsAuthority extends Frame
 {
     use ApiPagination;

     protected $validate = AuthorityValidate::class;

     /**
      * @see UserService
      * @var UserService
      */
     protected $usersService;
     /**
      * @see EpAuthorityService
      * @var epAuthorityService
      */
     protected $epAuthorityService;

     public function _initialize()
     {
         $this->usersService = UserService::singleton();
         $this->epAuthorityService = EpAuthorityService::singleton();
         parent::_initialize();
     }

     /**
      * 获取权限列表
      */
     public function getAuthority(){
         $roles = SessionManagement::getSession()->getUserRoles();
         $rules = $this->epAuthorityService->getRulesByRoles($roles);
         // 删除权限管理权限
         foreach ($rules as $key => $rule){
             if($rule->id == AuthRule::MODULE_AUTHORITY_ID){
                 array_splice($rules,$key,1);
                 break;
             }
         }
         $result = $this->epAuthorityService->getMenu($rules);
         return $this->result($result);
     }


     /**
      *  @OA\Get(
      *     tags={"权限管理相关"},
      *     path="/aicall/authority/getAuthMenus",
      *     summary="获取权限菜单",
      *     @OA\Response(response="200", description="请求成功"),
      *  )
      */
     public function getAuthMenus(){
         $roles = SessionManagement::getSession()->getUserRoles();
         $rules = $this->epAuthorityService->getRulesByRoles($roles);
         $menu = $this->epAuthorityService->getMenu($rules);
         $this->result($menu);
     }

     /**
      *  @OA\Get(
      *     tags={"权限管理相关"},
      *     path="/aicall/authority/getRolesByWhere",
      *     summary="获取角色列表",
      *     @OA\Parameter(name="name", in="query", @OA\Schema(type="string"), description="搜索关键字"),
      *     @OA\Parameter(name="page", in="query", @OA\Schema(type="integer"), description="页码"),
      *     @OA\Parameter(name="limit", in="query", @OA\Schema(type="integer"), description="条数"),
      *     @OA\Response(response="200", description="请求成功"),
      *  )
      */
     public function getRolesByWhere(){
         $this->checkRequest();

         $eid =  $this->getEid();
         $name = trim($this->request->param('name'));
         $paginationParams = $this->paginationParams();

         $result = $this->epAuthorityService->getRolesByPagination($eid,$name,$paginationParams);

         return $this->result($result);
     }


     /**
      *  @OA\Get(
      *     tags={"权限管理相关"},
      *     path="/aicall/authority/getRoleById",
      *     summary="获取角色详情",
      *     @OA\Parameter(name="id", in="query", @OA\Schema(type="integer"), required=true, description="id"),
      *     @OA\Response(response="200", description="请求成功"),
      *  )
      */
     public function getRoleById(){
         $this->checkRequest();
         $id = $this->request->param('id');
         $role = $this->epAuthorityService->getRoleById($id);
         if(boolval($role)){
             return $this->result($role);
         }else{
             throw new RoleException(ROLE_NOT_EXSIT);
         }
     }

     /**
      *  @OA\Post(
      *     tags={"权限管理相关"},
      *     path="/aicall/question/setRoleByData",
      *     summary="新建 / 修改 角色",
      *     @OA\Parameter(name="id", in="query", @OA\Schema(type="integer"), description="id"),
      *     @OA\Parameter(name="name", in="query", @OA\Schema(type="string"), required=true, description="角色名称"),
      *     @OA\Parameter(name="remark", in="query", @OA\Schema(type="string"), description="角色描述"),
      *     @OA\Parameter(name="status", in="query", @OA\Schema(type="integer"), required=true, description="角色状态 1 启用 0 关闭"),
      *     @OA\Parameter(name="auth_rules", in="query", @OA\Schema(type="string"), required=true, description="权限分配"),
      *     @OA\Response(response="200", description="请求成功"),
      *  )
      */
     public function setRoleByData(){

         $id = $this->request->param('id');
         $eid =  $this->getEid();
         if($id){
            $role = $this->modifyRole($eid, $id);
         }
         else{
            $role = $this->addRole($eid);
         }
         return $this->result($role);
     }

     public function addRole($eid){
         $data = [];
         $data['eid'] = $eid;
         $data['name']= $this->request->param('name');
         $data['remark'] = trim($this->request->param('remark'));
         $data['status'] = $this->request->param('status');
         $auth_rules = $this->request->param('auth_rules');

         if($this->usersService->roleNameExists($eid,$data['name'])){
             throw new RoleException(ROLE_NAME_EXITS);
         }
         $role = $this->usersService->addRole($data);
         $role = $this->usersService->bindAuthRule($role, $auth_rules);
         CacheActions::cache($role);
         return $role;
     }

     public function modifyRole($eid,$id){
         $data = [];
         $data['name']= $this->request->param('name');
         $data['remark'] = trim($this->request->param('remark'));
         $data['status'] = $this->request->param('status');
         $auth_rules = $this->request->param('auth_rules');

         $role = $this->usersService->getEpRole($eid,$id);

         if($role->name != $data['name']){
             if($this->usersService->roleNameExists($eid,$data['name'])){
                 throw new RoleException(ROLE_NAME_EXITS);
             }
         }

         $role = $this->usersService->updateRole($data,$role);
         // 删除原来的关系
         $this->usersService->unbindAuthRule($role);
         // 加入新的关系
         $this->usersService->bindAuthRule($role,$auth_rules);
         CacheActions::refresh($role);
     }

     /**
      *  @OA\Get(
      *     tags={"权限管理相关"},
      *     path="/aicall/authority/deleteRolesByIds",
      *     summary="（批量）删除角色",
      *     @OA\Parameter(name="ids", in="query", @OA\Schema(type="integer"), required=true, description="ids"),
      *     @OA\Response(response="200", description="请求成功"),
      *  )
      */
     public function deleteRolesByIds(){
         $this->checkRequest();
         $ids = $this->request->param('ids');
         $ids = explode(',',$ids);

         Db::startTrans();
         try{
             foreach ($ids as $id){
                 $this->deleteRoleById(intval($id));
             }
             Db::commit();
         } catch (\Exception $e) {
             Db::rollback();
         }


         return $this->result();
     }

     public function deleteRoleById($id) {
         $eid = $this->getEid();
         $role = $this->usersService->getEpRole($eid, $id);
         if($this->usersService->removeRole($role)){
             $this->usersService->unbindAuthRule($role);
         }
     }



     /**
      *  @OA\Get(
      *     tags={"权限管理相关"},
      *     path="/aicall/authority/controlRolesByIds",
      *     summary="（批量）启用或禁用角色",
      *     @OA\Parameter(name="ids", in="query", @OA\Schema(type="integer"), required=true, description="ids"),
      *     @OA\Response(response="200", description="请求成功"),
      *  )
      */
    public function controlRolesByIds(){
        $this->checkRequest();
        $ids = $this->request->param('ids');
        $status = $this->request->param('status');
        $ids = explode(',',$ids);
        Db::startTrans();
        try{
            foreach ($ids as $id){
                $this->controlRoleById($status,$id);
            }
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
        }

        return $this->result();
    }

    public function controlRoleById($status, $id){
        $eid = $this->getEid();
        $role = $this->usersService->getEpRole($eid, $id);
        $this->usersService->updateRole([
            'status' => $status
        ], $role);
    }

     /**
      * ---------------------------------------------- 账号管理部分分割线
      */
     /**
      *  @OA\Post(
      *     tags={"权限管理相关"},
      *     path="/aicall/authority/setUserByData",
      *     summary="新建 / 修改 账户",
      *     @OA\Parameter(name="id", in="query", @OA\Schema(type="integer"), description="账户id"),
      *     @OA\Parameter(name="username", in="query", @OA\Schema(type="string"), required=true, description="账号名称"),
      *     @OA\Parameter(name="password", in="query", @OA\Schema(type="string"), required=true, description="密码"),
      *     @OA\Parameter(name="role_ids", in="query", @OA\Schema(type="string"), required=true, description="角色ids"),
      *     @OA\Response(response="200", description="请求成功"),
      *  )
      */
     public function setUserByData(){
         $this->checkRequest();
         $id = $this->request->param('id');
        if($id){
            $this->modifyUser();
        }else{
            $this->addUser();
        }
     }

     public function addUser()
     {
         $eid = $this->getEid();
         $this->usersService->checkUsersLimit($eid);
         $username = $this->request->param("username");
         $password = $this->request->param("password");
         $mobile = $this->request->param("mobile");
         $role_ids = $this->request->param("role_ids");

         if ($this->usersService->loginExists($username) ) {
             throw new CoreException(LOGIN_EXITS);
         }
         if ($this->usersService->mobileExists($mobile) ) {
             throw new CoreException(MOBILE_EXITS);
         }
         // 添加用户
         $user =$this->usersService->addUser([
             "username"=>$username,
             "passwd"=>$password,
             "eid" => $eid,
             "mobile" => $mobile,
             "type"=>1
         ]);

         // 绑定用户与角色关系
         $user = $this->usersService->attachRoles($user, $role_ids);

         $this->result($user->visible(["username"])->toArray());
     }

     public function modifyUser()
     {
         $this->checkRequest();
         $eid = $this->getEid();
         $user_id = $this->request->param("id");
         $username = $this->request->param("username");
         $mobile = $this->request->param("mobile");
         $role_ids = $this->request->param("role_ids");

         $user = $this->usersService->getEpUser($eid, $user_id);

         if(empty($user)){
             throw new UserException(USER_NOT_FOUND);
         }
         if($user->username != $username){ /**更改用户名了*/
             if ($this->usersService->loginExists($username) ) {
                 throw new CoreException(LOGIN_EXITS);
             }
         }
         if($user->mobile != $mobile){ /**更改手机号*/
             if ($this->usersService->mobileExists($mobile) ) {
                 throw new CoreException(MOBILE_EXITS);
             }
         }

         $user = $this->usersService->modifyUser($user,[
             "username"=>$username,
             "mobile"=>$mobile
         ]);

         $this->usersService->detachRoles($user);
         $this->usersService->attachRoles($user, $role_ids);

         $this->result($user->visible(["username"])->toArray());
     }

     /**
      *  @OA\Get(
      *     tags={"权限管理相关"},
      *     path="/aicall/authority/getUsersByWhere",
      *     summary="获取账户列表",
      *     @OA\Parameter(name="name", in="query", @OA\Schema(type="string"), description="搜索关键字"),
      *     @OA\Parameter(name="page", in="query", @OA\Schema(type="integer"), description="页码"),
      *     @OA\Parameter(name="limit", in="query", @OA\Schema(type="integer"), description="条数"),
      *     @OA\Response(response="200", description="请求成功"),
      *  )
      */
     function getUsersByWhere(){
         $this->checkRequest();
         $eid =  $this->getEid();
         $name = trim($this->request->param('name'));

         $paginationParams = $this->paginationParams();

         $users = $this->epAuthorityService->getUsersByPagination($eid, $name, $paginationParams);

         return $this->result($users);
     }

     /**
      *  @OA\Get(
      *     tags={"权限管理相关"},
      *     path="/aicall/authority/resetPasswordById",
      *     summary="重置密码",
      *     @OA\Parameter(name="id", in="query", @OA\Schema(type="integer"), description="账户id"),
      *     @OA\Response(response="200", description="请求成功"),
      *  )
      */
     public function resetPasswordById(){
         $this->checkRequest();
         $eid = $this->getEid();
         $user_id = $this->request->param('id');
         $password = $this->request->param('password');
         $user = $this->usersService->getEpUser($eid, $user_id);
         $this->usersService->isPasswordValid($password,$user);
         $this->usersService->setPassword($user, $password);
         return $this->result($user->visible(["username"])->toArray());
     }

     /**
      *  @OA\Get(
      *     tags={"权限管理相关"},
      *     path="/aicall/authority/deleteUsersByIds",
      *     summary="（批量）删除账户",
      *     @OA\Parameter(name="ids", in="query", @OA\Schema(type="string"), description="账户ids"),
      *     @OA\Response(response="200", description="请求成功"),
      *  )
      */
     public function deleteUsersByIds(){
         $this->checkRequest();
         $user_ids = $this->request->param('ids');
         $user_ids = explode(',',$user_ids);
         Db::startTrans();
         try{
             foreach ($user_ids as $uid){
                 $this->deleteUserById($uid);
             }
             Db::commit();
         } catch (\Exception $e) {
             Db::rollback();
         }

         return $this->result();
     }

     public function deleteUserById($uid) {
         $eid = $this->getEid();
         $user = $this->usersService->getEpUser($eid,$uid);
         $result = $this->usersService->removeUser($user);
         return $result;
     }

     public function getAuthRule(){
         $rules = AuthRule::all([
             'parent_id' => ['<>', 0]
         ],'Actions');
         $this->result($rules);
     }

     public function getAllActions(){
         $actions = AuthAction::where(null)->order('name')->select();
         $this->result($actions);
     }

     public function setActionForRule(){
         $id = $this->request->param('id');
         $action_ids = $this->request->param('action_ids');

         $rule = AuthRule::get($id);
         $this->detachAction($rule);
         $this->bindAction($rule,$action_ids);

         return $this->result();
     }

     public function detachAction($rule){
         $actions = $rule->Actions;

         foreach ($actions as $action) {
             $rule->Actions()->detach($action->id);
         }
     }

     public function bindAction($rule,$action_ids){
         $action_ids = array_unique(explode(',',$action_ids));
         $rule->Actions()->attach($action_ids);
         return $rule;
     }

     public function addActionByData(){
         $name = $this->request->param('name');
         $title = $this->request->param('title');
         $action = new AuthAction();
         $existCount = $action->where([
             'name' => $name
         ])->count();

         if($existCount == 0){
             $action->save([
                 'name' => $name,
                 'title' => $title
             ]);
         }else{
             throw new CoreException(FIELD_IS_EXIST, '接口名已存在');
         }
         $this->result();
     }
 }
