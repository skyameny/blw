<?php
/**
 * Api控制器
 */
namespace api\controller;

use aicallup\service\EnterpriseService;
use community\service\CommunityService;
use core\controller\tool\EncryptionAttribute;
use api\service\ApiUserService;
use api\exception\TokenWillBeExpiredException;
use api\validate\ApiValidate;
use core\controller\Base;
use api\service\ApiService;
use core\includes\helper\HelperTime;
use api\model\ApiLog;

class Api extends Base
{
    use EncryptionAttribute;
 
    protected $requestCode = REQUEST_SUCCESS;

    protected $communityService;
    
    protected $beforeActionList = ["beforeLog"];
  
    protected $action_start_time = 0; //毫秒计算时间
  
    protected $token_expired_prompt = 0; //token即将过去提示错误码
    
    protected $validate = ApiValidate::class;
    protected $no_auth_action = []; //全小写
    
    protected $client_type = CLIENT_APP;

    public function _initialize()
    {
        parent::_initialize();
        
        if(!$this->authorizeSign()){
            $this->result("",AUTHOR_FAILED);
        }
        // 指定允许其他域名访问 
        header('content-type:application:json;charset=utf8');
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:POST');
        header('Access-Control-Allow-Headers:x-requested-with,content-type');
    }
    
    /**
     * action 前置操作
     */
    protected function beforeLog()
    {
        $this->action_start_time = HelperTime::getMillisecond();
    }
    
    /**
     * api log
     * 
     * {@inheritDoc}
     * @see \core\controller\Base::log()
     */
    public function log($status=ApiLog::STATUS_RESULT_SUCCESS, $level=1)
    {
        $apiService = ApiService::singleton();
        $enterprise = $this->getEnterprise();
        $apiService->log($this->action_start_time,$enterprise,$status);
    }
    /**
     * 验证签名
     */
    protected function authorizeSign(){
        $current_action = $this->request->action();
        if(in_array($current_action, $this->no_auth_action))
        {
           return true; 
        }
         $api_service = ApiService::singleton();
         try {
             $result = $api_service->authSign($this->request);
         } catch (TokenWillBeExpiredException $e) {
             
             $this->requestCode = $e->getCode();
             $result = true;
         }
         if(!$result){
             $this->result("",STATUS_AUTH_FAILED); //API验证失败
         } else {
             $this->enterpriseService = CommunityService::singleton();
         }
         return true;
    }
    
    
    //检查权限
    protected function authorize()
    {
        $current_action = $this->request->action();
        if(!in_array($current_action, $this->no_auth_action))
        {
            $access_token = $this->request->param("access_token");
            $api_user_service = ApiUserService::singleton();
            try {
                $result =$api_user_service->validateAuth($access_token);
            } catch (TokenWillBeExpiredException $e) {
                $this->token_expired_prompt = $e->getCode();
            }
            return true;
        }
        return true;
    }
    
    /**
     * 获取token过期提示
     * 可以根据是否为空来判断token是否即将过期 
     * 
     * @return number
     */
    protected function getTokenExpiredPrompt()
    {
        return $this->token_expired_prompt;
    }
    
    /**
     * 获取环境变量企业
     * 
     * @return NULL|unknown
     */
    protected function getEnterprise()
    {
        $current_action = $this->request->action();
        if(in_array($current_action, $this->no_auth_action)){
            return null;
        }
        return $this->communityService->getEnterprise();
    }
    
    final protected function result($data = '', $code = 0, $msg = '', $type = '', array $header = [])
    {
        if($code == $this->requestCode || $code == 0)
        {
            $code = $this->requestCode;
            $this->log($code);
        }
        parent::result($data, $code, $msg, $type, $header);
    }
}
?>