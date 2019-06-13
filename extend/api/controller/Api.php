<?php
/**
 * Api控制器
 */
namespace api\controller;

use authority\includes\user\storage\DtAuthStorage;
use authority\service\IdentifyService;
use community\service\CommunityService;
use api\validate\ApiValidate;
use core\controller\Base;
use api\service\ApiService;
use api\model\ApiLog;
use core\includes\helper\HelperTime;

class Api extends Base
{
    /**
     * @var CommunityService
     */
    protected $communityService;

    /**
     * @var IdentifyService
     */
    protected $identify_service;
    
    protected $beforeActionList = ["beforeLog"];
  
    protected $action_start_time = 0; //毫秒计算时间
  
    protected $token_expired_prompt = 0; //token即将过去提示错误码
    
    protected $validate = ApiValidate::class;

    protected $no_auth_action = []; //全小写
    
    protected $client_type = CLIENT_APP;

    public function _initialize()
    {
        #APP验证机制为database token
        $this->identify_service = IdentifyService::singleton();
        $this->identify_service->setStorage(new DtAuthStorage());

        parent::_initialize();
        if(!$this->authorize()){
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
        $enterprise = $this->getCommunity();
        $apiService->log($this->action_start_time,$enterprise,$status);
    }

    //检查权限
    protected function authorize()
    {
        $current_action = $this->request->action();
        if(!in_array($current_action, $this->no_auth_action))
        {
            return $this->identify_service->authenticate();
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
     *  获取环境变量社区
     * @return bool|mixed|null
     */
    public function getCommunity()
    {
        $this->communityService = CommunityService::singleton();
        $current_action = $this->request->action();
        if(in_array($current_action, $this->no_auth_action)){
            return null;
        }
        $cid = $this->request->header(ApiService::HEADER_NAME_COMMUNITY_ID);
        $communities = $this->communityService->getCommunities(["cid"=>$cid]);
        if(empty($communities)){
            return false;
        }else{
            return current($communities);
        }
    }
    
    final protected function result($data = '', $code = 0, $msg = '', $type = '', array $header = [])
    {
        if($code == STATUS_CODE_SUCCESS || $code == 0)
        {
            $code = STATUS_CODE_SUCCESS;
            #$this->log($code);
        }
        parent::result($data, $code, $msg, $type, $header);
    }
}
?>