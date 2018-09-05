<?php
/**
 * API接口验证器
 * @author Dream<hukaijun@emicnet.com>
 */
namespace app\account\validate;

use think\Validate;
use core\includes\helper\HelperPassword;
use core\exception\PasswordException;

class BlValidate extends Validate
{
    protected $rule = array(
        'username'           => 'require|isUsername',
        'passwd'             => 'require',
        "password"           => 'require|isPasswd',
        'captcha|验证码'      =>'require|captcha',
        
        'role_name'=>'require',
    );
    
    protected $message = array(
        'username.require'     => "用户名不能为空",
        'username.isUsername'  => "用户名不符合要求",
        'password.isPasswd'   => "密码格式不正确",
        'password.require'    => "密码不能为空",
        'passwd.require'      => "密码不能为空",
        'role_name.require'       =>  "角色名称不能为空",
        
        
    );
    
    protected  $scene = array(
        'login' => ["passwd","username","captcha"],
        //startTask
        'addRole' =>["role_name"],
        //getToken
        'gettoken' =>["ep_id","ep_name","mt_domain","es_domain"],
        //修改企业
        'modifyep' => ["ep_name","mt_domain","ep_id","record_auth",'token','es_domain'],
        //修改任务
        'modifytask' => ["task_name","task_id","quality_inspection_item","token","call_list"],
        //注册
        'registerep' => ["ep_name","mt_domain","ep_id","record_auth",'es_domain'],
        //提交质检任务
        'inspectquality' => ['task_id','task_name','quality_inspection_item','token'],
        //查询质检状态
        'queryinspectionstate' => ['quality_inspection_id','token'],
        //查询质检结果
        'queryinspectionresult' => ['quality_inspection_id','count','page','token'],
        //暂停质检
        'pauseinspectquality' => ['quality_inspection_id','token'],
        //重新质检
        'reinspectquality' => ['quality_inspection_id','token'],
        //删除质检任务
        'deleteinspectquality' => ['quality_inspection_id','token'],
    );
    
    protected function isDomain($value)
    {
        $match='/^(http|https):[\/]{2}[a-zA-Z0-9]+[.]{1}[a-z\d\-]+[.]{1}[a-z\d]*[\/]*[A-Za-z\d]*[\/]*[A-Za-z\d]*/';
        return !!preg_match($match,$value);
    }

    protected function isJson($value)
    {
        return !!(json_decode($value));
    }
    
    /**
     * 用户名规则 数字字母_ 3-20位
     */
    protected function isUsername($value)
    {
        $match = "/^[A-Za-z0-9_]{3,20}+$/";
        return !!preg_match($match,$value);
    }
    
    protected function isPasswd($value)
    {
        try {
            HelperPassword::validate($value);
        } catch (PasswordException $e) {
            return false;
        }
        return  true;
    }
    
    protected function isToken($value) 
    {
        $enterprise = Enterprise::get(["token"=>$value]);
        return !is_null($enterprise);
    }
}