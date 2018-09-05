<?php
/**
 *  管理员界面统一入口
 *  
 */
namespace app\account\controller;
use core\controller\Account;
use core\models\Operator;
use think\Config;
use core\includes\helper\helperUser;

class Index extends Account
{
    //控制台
    public function index()
    {
        $accountThemePath    = Config::get('default_theme_path');
        $accountDefaultTheme = "angulr_account/";
        $themePath = "{$accountThemePath}{$accountDefaultTheme}";
        $root = $this->request->root;
        return  $this->fetch($root.$themePath."index.html");
    }
    
    
    /**
     * 商业管理
     */
    public function business()
    {
        
    }
    
    
    public function loadDefaultData()
    {
        $session = SessionMagament::getSession()->getSession();
        $operator = Operator::get($session->get("operatorid"));
        $coadmin =  $session->getUser();
    }
    
    
    /**
     * 获取用户的详细信息
     */
    public function getUserInfo()
    {
        
    }
    

    
}
