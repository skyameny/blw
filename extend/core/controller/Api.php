<?php
/**
 * Api控制器
 */
namespace core\controller;

use core\controller\tool\EncryptionAttribute;
use api\service\ApiUserService;
use api\exception\TokenWillBeExpiredException;
use api\validate\ApiValidate;
use community\model\Community;

class Api extends Base
{
    use EncryptionAttribute;
    
    protected static $garden = null;
    
    protected $token_expired_prompt = 0; //token即将过去提示错误码
    
    protected $validate = ApiValidate::class;
    protected $no_auth_action = []; //全小写
    
    protected $client_type = CLIENT_APP;
    
    public function _initialize()
    {
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
     * api输出
     * @param unknown $data
     */
    public function apiResult($data)
    {
        return $this->result($data);
    }
    /**
     * 获取环境变量
     *
     * @return NULL|unknown
     */
    protected function getGarden()
    {
        $current_action = $this->request->action();
        if(in_array($current_action, $this->no_auth_action)){
            return null;
        }
        if(is_null(self::$garden)){
            self::$garden = Community::get(API_ENTERPRISE_ID);
        }
        return self::$garden;
    }
}
?>