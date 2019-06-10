<?php
/**
 *  平台公共页面
 */
namespace app\account\controller;
use core\controller\Account;
use core\service\UserService;
use core\includes\session\SessionManagement;
use think\captcha\Captcha;
use core\model\User;

class Storage extends Account
{
    protected $no_auth_action = ["login","register"];

    /**
     * 登录login
     */
    public function login()
    {
        if (SessionManagement::isAnonymous()) {
            $this->checkRequest();
            $account = $this->request->param("userName");
            $pwd = $this->request->param("password");
            $type = $this->request->param("type");
            $captcha = $this->request->param("captcha");
            $result = UserService::singleton()->loginUser($account, $pwd);
            $userid = SessionManagement::getSession()->getUserPropertyValues("id");
            $userResource = User::get($userid);
            // 更新登录状态
            $userResource->isUpdate(true)->save([
                "last_ip" => $this->request->ip(),
                "last_login" => NOW_TIME
            ]);
            $this->log("登录系统");
            $this->result("");
        }
        $this->result("");
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
        $this->result("");
    }

    public function test()
    {
        echo "Heool";
    }
}