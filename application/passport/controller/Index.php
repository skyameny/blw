<?php
/**
 *  管理员界面统一入口
 *  
 */
namespace app\passport\controller;
use think\Config;
use core\includes\session\SessionManagement;
use core\service\UserService;
use core\controller\Frame;

class Index extends Frame
{
    protected $no_auth_action = ["login","logout","index"]; //全小写
    
    //系统入口
    public function index()
    {
        $accountThemePath    = Config::get('default_theme_path');
        $accountDefaultTheme = "angulr_account/";
        $themePath = "{$accountThemePath}{$accountDefaultTheme}";
        $root = $this->request->root;
        return  $this->fetch($root.$themePath."index.html");
    }
    
    /**
     * 系统登录
     * @return mixed|string
     */
    public function login()
    {
        $userinfo = [];
        $this->checkParams();
        if(SessionManagement::isAnonymous()){
            $result = UserService::singleton()->loginUser($this->request->param("username"),$this->request->param("passwd"));
            if(!$result){
                $this->result("",STATUS_CODE_LOGIN_FAILED);//所有登录错误 统一返回用户名密码错误
            }
        }
        $session = SessionManagement::getSession();
        $userinfo["uid"] = $session->getUserId();
        $userinfo["nickname"] = $session->getUserLabel();
        $userinfo["username"] = $session->getUserPropertyValues("username");
        $userinfo["type"] = $session->getUserPropertyValues("type");
        $userinfo["enterprise"] = $session->getUserPropertyValues("gid");
        $userinfo["role"] = $session->getUserRoles();
        $this->log("登录系统");
        $this->result($userinfo);
    }
    
    /**
     * 退出系统
     */
    public function logout()
    {
        $this->log("退出系统");
        SessionManagement::endSession();
        $this->result("");
    }
   
    /**
     * 获取用户的详细信息
     */
    public function getUserInfo()
    {
        
    }
    
}
