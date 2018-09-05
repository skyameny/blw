<?php
/**
 *  平台公共页面
 */
namespace app\account\controller;
use core\controller\Account;
use core\service\UserService;
use core\includes\session\SessionManagement;
use think\captcha\Captcha;
use think\facade\Config;
use think\Session;
use core\models\User;

class Storage extends Account
{
    protected $no_auth_action = ["login","register"];
    
    
    /**
     * 登录login
     */
    public function login()
    {
        if(!$this->request->isPost()){
            if(SessionManagement::isAnonymous()){
                return $this->fetch();
            }
            $userLevel = SessionManagement::getSession()->getUserPropertyValues("level");
            $redirectUrl = ($userLevel ==0)?"/admin":"/";
            //强制定位到主页
            $this->redirect($redirectUrl);
        }
        //action
        $this->checkParams();
        $go = $this->request->param("go");
        $account = $this->request->param("username");
        $pwd = $this->request->param("passwd");
        $captcha = $this->request->param("captcha");
        $result = UserService::singleton()->loginUser($account,$pwd);
        $userid = SessionManagement::getSession()->getUserPropertyValues("id");
        $userResource = User::get($userid);
        //更新登录状态
        $userResource->isUpdate(true)->save(["last_ip"=>$this->request->ip(),"last_login"=>NOW_TIME]);
        $this->log("登录系统");
        
        if(!empty($go)){
            $this->redirect($go);
        }
        $this->result(["url"=>$go]);
    }
    
    /**
     * 退出
     */
    public function logout()
    {
        $userid = SessionManagement::getSession()->getUserPropertyValues("id");
        $userResource = User::get($userid);
        $userResource->isUpdate(true)->save(["last_ip"=>$this->request->ip(),"last_login"=>NOW_TIME]);
        $this->log("退出系统");
        SessionManagement::endSession();
        $this->redirect("/account/storage/login");
    }
    
    
}