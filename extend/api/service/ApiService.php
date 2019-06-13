<?php
/**
 * 提供API公共方法
 * 
 */

namespace api\service;

use core\service\Service;
use api\model\ApiUser;
use api\exception\ApiException;
use core\utils\ExLog;
use think\Request;
use api\model\ApiLog;
use core\includes\helper\HelperTime;

class ApiService extends Service
{
    const HEADER_NAME_COMMUNITY_ID = "auth-community";
    const HEADER_NAME_ACCESS_TOKEN = "";
    const HEADER_NAME_MEMBER_MID = "";





    private static $enterprise = null;
    /**
     * 获取token
     */
    public function getToken($user)
    {
        
    }
    
    /**
     * 验证签名
     * 
     * @param unknown $auth
     * @param unknown $sign    md5(sercret:token:timestamp)
     * @throws ApiException
     */
    public function authSign(Request $request)
    {
        $orginAuth= $request->header("Authorization");
        $sign = $request->header("Sign");
        $auth = urldecode(base64_decode($orginAuth));
        if(empty($auth) ||empty($sign)){
            $this->result("",STATUS_AUTH_FAILED); //API验证失败
        }
        if (strpos($auth, ":") === false && strlen($sign) !== 32) {
            ExLog::log("表头不合法");
            throw new ApiException(STATUS_AUTH_FAILED);
        }
        $auth_arr = explode(":", $auth);
        $access_token = $auth_arr[0];
        $timestamp = $auth_arr[1];
        ExLog::log("access_token:".$access_token."|timestamp:".$timestamp,ExLog::INFO);
        $api_user_service = ApiUserService::singleton();
        $result = $api_user_service->validateAuth($access_token);
        if (! $result || empty($timestamp)) {
            ExLog::log("TOKEN不合法");
            throw new ApiException(STATUS_AUTH_FAILED);
        }
        $api_user = $api_user_service->getApiUserByToken($access_token);
        if (is_null($api_user)) {
            ExLog::log("没有对应的用户:".$access_token);
        }
        // 验证签名
        $server_sign = md5($api_user->getAttr("secret")  . $orginAuth);
        if ($server_sign !== $sign) {
            ExLog::log("没有对应的用户${server_sign}!=${sign}");
            throw new ApiException(STATUS_AUTH_FAILED);
        }
        return true;
    }
    
    /**
     * 验证appid和secret
     * 
     * @param unknown $appid
     * @param unknown $secret
     */
    public function authUser($appid,$secret)
    {
        $dao = new ApiUserDao();
        $users = $dao->findByWhere(["appid"=>$appid,"secret"=>$secret,"status" => ApiUser::STATUS_ENABLE]);
        if(empty($users)){
            throw new ApiException(STATUS_INVALID_APPID);
        }
        self::$enterprise = EnterpriseDao::get($users[0]->getAttr("eid"));
        return $users[0];
    }
    
    /**
     * 记录到日志
     * @param unknown $request
     */
    public function log($start_time,$enterprise=null,$status =ApiLog::STATUS_RESULT_SUCCESS)
    {
        $request = Request::instance();
//         $params = $request->param();
//         $header["Sign"] = $request->header("Sign");
//         $header["Authorization"] = $request->header("Authorization");
//         $params = array_merge($header,$params);
//         foreach ($params as $key=>$param)
//         {
//             if($param instanceof File){
//                 $params[$key] = $param->getSaveName();
//             }
//         }
        if(empty($enterprise)){
            $enterprise = self::$enterprise;
        }
        $apiuser = ApiUserService::singleton()->getApiUser($enterprise);
        //$logdata["params"] = json_encode($params);
        $logdata["ip"] = $request->ip();
        $logdata["time"] = NOW_TIME;
        $logdata["api_name"] = $request->module()."/".$request->controller()."/".$request->action();
        $logdata["duration"] = HelperTime::getMillisecond()-$start_time;
        $logdata["eid"] = $apiuser?$apiuser->getAttr("eid"):0;
        $logdata["api_uid"] = $apiuser?$apiuser->getAttr("id"):0;
        $logdata["status"] = $status;
        $api_log = new ApiLog();
        $api_log->save($logdata);
        ExLog::log("保存API请求日志");
    }

}