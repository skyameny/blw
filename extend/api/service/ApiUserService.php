<?php
/**
 * @desc api用户服务
 * 
 * @author Dream<hukaijun@emicnet.com>
 */
namespace api\service;

use core\service\Service;

use api\exception\ApiException;
use api\model\ApiUser;
use api\model\AccessToken;
use api\exception\TokenWillBeExpiredException;
use core\utils\ExLog;
use api\includes\helper\HelperApi;
use think\Request;

class ApiUserService extends Service
{
    //企业最大apisuer限制
    const ENTERPRISE_APIUSER_LIMIT = 1;
    
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
        return $users[0];
    }

    /**
     * 验证access_token的合法性
     * 
     * @param string $access_token            
     * @return bool
     */
    public function validateAuth($access_token)
    {
        $api_user = ApiUser::hasWhere('access_token', ["access_token" => $access_token])->order("id desc")->find();

        if (empty($api_user) ||$api_user->getAttr("status") == ApiUser::STATUS_DISABLE)
        {
            ExLog::log("不存在该用户",ExLog::INFO);
            throw new ApiException(STATUS_API_TOKEN_EXPIRE); // token无效
        }
        $eid = $api_user->getAttr("eid"); 
        defined("API_ENTERPRISE_ID") or define("API_ENTERPRISE_ID", $eid);
        if(!$api_user->enterprise->isApi()){
            ExLog::log("企业未开通API功能",ExLog::INFO);
            throw new ApiException(STATUS_ENTERPEISE_NOT_API); // token无效
        }
        $token = $api_user->access_token()->where(["access_token" => $access_token])->find();
        if (empty($token) || $token->status() === AccessToken::TOKEN_STATUS_EXPIRED) 
        {
            ExLog::log("TOKEN过期",ExLog::INFO);
            throw new ApiException(STATUS_API_TOKEN_EXPIRE); // token无效
        }
        else if ($token->status() === AccessToken::TOKEN_STATUS_RESERVED) {
            ExLog::log("TOKEN即将过期",ExLog::INFO);
            throw new TokenWillBeExpiredException();
        }
        return true;
    }
    
    /**
     * 创建ApiUser 用户
     * @param unknown $enterprise
     */
    public function createUser($enterprise)
    {
        $api_user = $this->getApiUser($enterprise);
        if(!empty($api_user)){
            throw new CoreException(STATUS_ENTERPEISE_APIUSER_OVER);
        }
        $api_user = new ApiUser();
        $data["eid"] = $enterprise->getAttr("id");
        $data["appid"] = HelperApi::createAppid($data["eid"]);
        $data["secret"] = HelperApi::createSecret();
        $data["status"] = ApiUser::STATUS_ENABLE;
        $data["create_time"] = NOW_TIME;
        $api_user->save($data);
        ExLog::log("正在创建API用户[".$data["appid"]."]",ExLog::INFO);
        return $api_user;
    }
    
    /**
     * 获取用户
     * 
     * @param unknown $access_token
     * @return array|\think\db\false|PDOStatement|string|\think\Model
     */
    public function getApiUserByToken($access_token)
    {
        $api_user = ApiUser::hasWhere('access_token', [
            "access_token" => $access_token
        ])->order("id desc")->find();
        return $api_user;
    }
    
    
    
    /*
     * 
     * @return ApiUser
     */
    public function getApiUser($enterprise)
    {
        $result = ApiUser::get(["eid"=>$enterprise->getAttr("id")]);
        return $result;
    }
    
    /**
     * 
     * @param unknown $enterprise
     */
    public function BanUser($enterprise)
    {
     //   $users = ApiUserDao::all(["eid"=>$enterprise->getAttr("id"),"status"=>ApiUser::STATUS_ENABLE]);
      $result =  ApiUser::where(["eid"=>$enterprise->getAttr("id"),"status"=>ApiUser::STATUS_ENABLE])
        ->update(["status"=>ApiUser::STATUS_DISABLE]);
      ExLog::log("设置企业[".$enterprise->getAttr("name")."]API用户禁用",ExLog::INFO);
      return $result;
    }
    
    /**
     * 创建token
     * 新的token会直接覆盖旧的token
     * 时间会刷新
     * 
     */
    public function createToken(ApiUser $user,$ip)
    {
        //删除现有的token
        $user->access_token()->delete();
        $token = $user->createAccessToken($ip);
        return $token;
    }
    
    /**
     * 
     * @param unknown $eid
     */
    public function getToken(Enterprise $enterprise)
    {
        $access_token = "";
        $apiUser = $this->getApiUser($enterprise);
        if($apiUser){
            $request = Request::instance();
            $access_token =$apiUser->access_token()->find();
        }
        return empty($access_token)?"":$access_token->getAttr("access_token");
    }
}