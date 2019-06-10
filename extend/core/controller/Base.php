<?php
/**
 * 基础控制器
 */
namespace core\controller;

use core\utils\ExLog;
use think\Controller;
use think\exception\HttpResponseException;
use think\Request;
use think\Response;
use think\Config;
use core\service\ServiceManagement;
use core\includes\session\SessionManagement;
use core\exception\CoreException;

abstract class Base extends Controller
{
    /**
     * 客户端类型
     */
    protected $client_type;
    /**
     * 参数验证
     */
    protected $validate;

    #逻辑层
    protected $logic = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->request = Request::instance();
        /*防止跨域*/
        //header('Access-Control-Allow-Origin: '.$_SERVER['HTTP_ORIGIN']);
        //header('Access-Control-Allow-Credentials: true');
        //header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        //header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, authKey, sessionId");
    }

    // 请求参数验证
    protected function checkRequest($message = array(), $batch = false, $callback = null)
    {
        try {
            if (empty($this->validate)) {
                $this->result_failed(STATUS_CODE_PARAM_ERROR);
            }
            $result = $this->validate($this->request->param(), $this->validate . '.' . $this->request->action(), $message, $batch, $callback);
            if (is_numeric($result)) {
                $this->result_failed($result);
            } elseif (is_string($result)) {
                $this->result_failed(STATUS_CODE_PARAM_ERROR, $result);
            }
        } catch (CoreException $e) {
            $code = $e->getCode();
            $code = $code ? $code : STATUS_CODE_PARAM_ERROR;
            $this->result_failed($code, $e->getMessage());
        }
    }

    final protected function result_failed($code = STATUS_CODE_SYSTEM_ERROR, $msg = '')
    {
        $this->result('', $code, $msg);
    }

    final protected function result_success($data = '')
    {
        $this->result($data, STATUS_CODE_SUCCESS);
    }

    protected function result($data = '', $code = 0, $msg = '', $type = '', array $header = [])
    {
        if(empty($msg)){
            $msg = Config::has(ERROR_PREFIX.$code)?Config::get(ERROR_PREFIX.$code):"";
        }
        if($code == STATUS_CODE_SUCCESS){
            $msg = "SUCCESS";
        }
        $result = [
            'status' => $code,
            'info'  => $msg,
            'time' => $_SERVER['REQUEST_TIME'],
            'data' => empty($data)? is_array($data) ? [] : $data: $data,//Java解析null、数组对象
        ];
        ExLog::info("返回结果>>${code}:${msg}");
        $type     = $type ?: $this->getResponseType();
        $response = Response::create($result, $type)->header($header);
        throw new HttpResponseException($response);
    }

    /**
     * 服务器记录系统管理员日志
     * @param string $message
     * @param number $level
     */
    protected function log($message, $level=1)
    {
        $single = ServiceManagement::singleton()->get(SYSTEM_SERVICE);
        $user = SessionManagement::getSession()->getUser()->getUserResource();
        $single->log(null,$user,$message,$level);
    }

    /**
     * 记录此行为为一个危险操作
     * @param $message
     */
    protected function logDanger($message)
    {
        $this->log($message,Log::LOG_LEVEL_DANGER);
    }

    /**
     * 记录此行为为一个警告操作
     * @param $message
     */
    protected function logWarning($message)
    {
        $this->log($message,Log::LOG_LEVEL_WARING);
    }
}