<?php
/**
 * Api控制器
 */
namespace api\controller;

use api\service\ApiUserService;
use api\exception\TokenWillBeExpiredException;
use api\validate\ApiValidate;
use aicallup\model\Enterprise;
use core\controller\Api;

abstract class ApiCommon extends Api
{

    protected static $enterprise = null;

    protected $token_expried_prompt = 0;
    // token即将过去提示错误码
    protected $validate = ApiValidate::class;

    protected $no_auth_action = [];
    // 全小写
    protected $client_type = CLIENT_APP;

    public function _initialize()
    {
        parent::_initialize();
        
        if (! $this->authorize()) {
            $this->result("", AUTHOR_FAILED);
        }
        // 指定允许其他域名访问
        header('content-type:application:json;charset=utf8');
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:POST');
        header('Access-Control-Allow-Headers:x-requested-with,content-type');
    }
    /**
     * 检查权限
     * 
     */ 
    protected function authorize()
    {
        $current_action = $this->request->action();
        if (! in_array($current_action, $this->no_auth_action)) {
            $access_token = $this->request->param("access_token");
            $api_user_service = ApiUserService::singleton();
            try {
                $result = $api_user_service->validateAuth($access_token);
            } catch (TokenWillBeExpiredException $e) {
                $this->token_expried_prompt = $e->getCode();
            }
            return true;
        }
        return true;
    }


}
?>