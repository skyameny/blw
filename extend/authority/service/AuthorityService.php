<?php
/**
 * 权限控制类
 * User: silkshadow
 * @author Dream<Hukaijun@emicnet.com>
 * Update by 2019年05月14日
 * @see authentication
 * @see inActionList
 *
 *
 * Date: 2019-04-30
 * Time: 09:46
 */

namespace authority\service;

use authority\dao\AuthorityDao;
use authority\dao\RoleDao;
use authority\dao\UserDao;
use authority\model\AuthAction;
use authority\utils\CacheActions;
use core\includes\user\User;
use core\model\Role;
use core\service\Service;
use authority\model\AuthRule;
use core\utils\ExLog;

class AuthorityService extends Service implements AuthorityManagement,RuleManagement
{

    protected $authorityDao;
    protected $roleDao;
    protected $userDao;

    protected $authRuleVisible = ['id','name','title','children'];

    public function __construct()
    {
        parent::__construct();
        $this->authorityDao = new AuthorityDao();
        $this->roleDao = new RoleDao();
        $this->userDao = new UserDao();
    }

    /**
     * 全局权限控制器函数
     * 支持参数
     * @param Role $role
     * @param $action
     * @param array $params
     * @return bool|mixed
     * @throws \think\Exception
     */
    public static function authentication(Role $role, $action,$params=array())
    {
        #如果不在规则表中
        $actionModel = new AuthAction();
        $count = $actionModel->where('name','like',$action."%")->count();
        if($count ==0){
            return true;
        }
        #管理员规则
        if($role->isSysAdmin()){
            return true;
        }
        #基准规则
        return self::inActionList($role,$action,$params);
    }

    /**
     * 角色匹配规则
     * 例如$action =  /aicall/enterprise/importData:type=2:status=4
     * @param $role
     * @param $action
     * @param $params
     * @return bool
     */
    protected static function inActionList(Role $role,$action,$params)
    {
        $actionList= CacheActions::getCache($role);
        if(empty($actionList)){
            ExLog::log("正在更新系统角色[".$role->getAttr("id")."]缓存");
            CacheActions::cache($role);//兼容CACHE丢失
            $actionList = CacheActions::getCache($role);
        }
        if(empty($actionList)){
            return false;
        }
        foreach ($actionList as $action_item){
            if(strpos($action_item,$action) !==0)
            {
                continue;
            }
            #匹配到相同的action接口
            if(strpos($action_item,":")===false)
            {
                return true;
            }
            $p_slice = explode($action_item,":");
            foreach ($p_slice as $_key => $_value){
                if($_key == 0){
                    continue;
                }
                #只有params名称
                if(strpos($_value,"=") ===false){
                    return isset($params[$_value])&&!empty($params[$_value]);
                }
                #含有特定规则
                $s_slice = explode($_value,"=");
                return isset($params[$s_slice[0]]) && ($params[$s_slice[0]] ==$s_slice[1]);
            }
        }
        return false;
    }

    // 获取所有权限
    public function getAllAuthority(){
        return $this->authorityDao->getAuthorityTree();
    }

    // 获取权限菜单栏
    public function getMenu($roles)
    {

    }


    public function getMenuTree($rules, $pid = 0)
    {
        $result = $rules[$pid];
        foreach ($rules[$pid] as &$rule) {
            $ruleId = $rule['id'];
            if(isset($rules[$ruleId])){
                $rule['children'] = $this->getMenuTree($rules, $ruleId);
            }
        }
        return $result;
    }

    public function getParentByPid($pid){
        $result = AuthRule::where([
            'id' => $pid
        ])->find();
        $result->visible($this->authRuleVisible)->toArray();
        return $result;
    }

    public function addRuleForRole(AuthRule $rule, Role $role)
    {
        // TODO: Implement addRuleForRole() method.
    }

    public function getAllRule(Role $role, $show_disable = false)
    {
        $rules = [];

        if($show_disable) {
            $rules = $role->authRule;
        }
        else {
            if($role['status'] == Role::STATUS_ENABLE){
                $rules = $role->authRule;
            }
        }
        return $rules;
    }

    public function getAuthRule(User $user)
    {
        // TODO: Implement getAuthRule() method.
    }

    public function hasRule(Role $role, AuthRule $rule)
    {
        // TODO: Implement hasRule() method.
    }
    public function removeRuleForRole(AuthRule $rule, Role $role)
    {
        // TODO: Implement removeRuleForRole() method.
    }

    public function bindRole(AuthRule $rule, Role $role)
    {
        // TODO: Implement bindRole() method.
    }
    public function unbindRole(AuthRule $rule, Role $role)
    {
        // TODO: Implement unbindRole() method.
    }

    #扩展
    /**
     * 获取企业的代理管理员权限
     */
    public function getEpAgentRole()
    {
        return Role::get(DEFAULT_AGENT_ROLE_ID);
    }

    /**
     * @param $roles
     *
     * 登录成功后调用
     *
     * $roles  array
     * AuthorityService::singleton()->cacheRolesAction($roles);
     *
     */
    public function cacheRolesAction($roles){
        foreach ($roles as $role){
            if (CacheActions::has($role)){
                return ;
            }else{
                CacheActions::cache($role);
            }
        }
    }
}