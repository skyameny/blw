<?php
/**
 * 基础控制器
 */
namespace core\controller;

use think\Controller;
use think\exception\HttpResponseException;
use think\Response;
use think\Config;
use think\Request;
use think\View;
use core\service\ServiceManagement;
use core\includes\session\SessionManagement;
use core\controller\tool\Authentication;

class Base extends Controller
{
    use Authentication;
    /**
     * 客户端类型
     * 
     * @var unknown
     */
    protected $client_type;

    protected $validate = "think\\Validate";

    public function __construct(Request $request = null)
    {
        if (is_null($request)) {
            $request = Request::instance();
        }
        
        $this->request = $request;
        
        $this->view = View::instance(Config::get('template'), Config::get('view_replace_str'));
        
        // 控制器初始化
        $this->_initialize();
        
        // 前置操作方法
        if ($this->beforeActionList) {
            foreach ($this->beforeActionList as $method => $options) {
                is_numeric($method) ? $this->beforeAction($options) : $this->beforeAction($method, $options);
            }
        }
    }

    // 重写jump result
    public function _initialize()
    {
        if(!in_array($this->request->action(), $this->no_auth_action))
        {
            if(!$this->verification()){
                $this->result("",STATUS_CODE_PERMISSION_DEND);
            }
        }
        parent::_initialize();
        /* 防止跨域 */
        // header('Access-Control-Allow-Origin: '.$_SERVER['HTTP_ORIGIN']);
        // header('Access-Control-Allow-Credentials: true');
        // header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        // header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, authKey, sessionId");
    }

    final protected function result($data, $code = 0, $msg = '', $type = '', array $header = [])
    {
        if (Config::has(ERROR_PREFIX . $code) && empty($msg)) {
            $msg = Config::get(ERROR_PREFIX . $code);
        }
        if ($code == STATUS_CODE_SUCCESS) {
            $msg = "SUCCESS";
        }
        $result = [
            'status' => $code,
            'info' => $msg,
            'time' => $_SERVER['REQUEST_TIME'],
            'data' => empty($data) ? null : $data // Java解析null、数组对象
        ];
        $type = $type ?: $this->getResponseType();
        $response = Response::create($result, $type)->header($header);
        throw new HttpResponseException($response);
    }

    /*
     * 服务器记录系统管理员日志
     * @param unknown $message
     * @param number $level
     */
    public function log($message, $level = 1)
    {
        $single = ServiceManagement::singleton()->get(SYSTEM_SERVICE);
        $user = SessionManagement::getSession()->getUser()->getUserResource();
        $single->log(null, $user, $message, $level);
    }

    /**
     * 请求参数验证
     * step1 :验证session权限
     * step2 :验证role权限
     * setp3 :验证参数列表
     * 
     * @param array $message
     * @param string $batch
     * @param unknown $callback
     */
    protected function checkRequest($validate=null)
    {
        $flag = $this->validate($this->request->param(), $this->validate . "." . $this->request->action());
        if ($flag !== true) {
            if (Config::has(ERROR_PREFIX . $flag)) {
                $msg = Config::get(ERROR_PREFIX . $flag);
                $error_code = $flag;
            }
            $msg = $msg ?? $flag;
            $error_code = $error_code ?? STATUS_CODE_PARAM_ERROR;
            $this->result('', $error_code, (string) $msg);
        }
    }
    
}
?>