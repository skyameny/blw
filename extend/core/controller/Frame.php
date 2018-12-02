<?php
/**
 * APISESSION 控制器
 */
namespace core\controller;

use think\Controller;
use core\includes\session\SessionManagement;

class Frame extends Base
{

    protected $no_auth_action = [];

    public function _initialize()
    {
        $this->client_type = CLIENT_WEBSITE;
        $this->authSession();
        parent::_initialize();
    }
    
    /**
     * 条件过滤
     * @param array $dict
     */
    protected function filter($dict=[])
    {
        $returnValue = [];
        $this->checkRequest();
        foreach ($dict as $key =>$value){
            $_key = is_string($key)?$key:$value;
            $returnValue[$_key] = $this->request->param($value);
        }
        return $returnValue;
    }

    /**
     * 检查验证请求间隔
     *
     * @param unknown $duration            
     */
    protected function checkLastAction($duration)
    {
        $action = $this->request->module() . "-" . $this->request->controller() . "-" . $this->request->action();
        $time = time();
        $session_last_action = session('last_action');
        if (! empty($session_last_action['action']) && $action == $session_last_action['action']) {
            $mduration = $time - $session_last_action['time'];
            if ($duration > $mduration) {
                $this->result("", STATUS_CODE_ILLEGAL_OPRATION, "您的操作太过频繁，请稍后再试");
            } else {
                session('last_action.time', $time);
            }
        } else {
            session('last_action.action', $action);
            session('last_action.time', $time);
        }
    }

    /**
     * 验证用户session是否过期
     */
    protected function authSession()
    {
        $current_action = $this->request->action();
        if (! in_array($current_action, $this->no_auth_action)) {
            if (SessionManagement::isAnonymous()) {
                $this->result("", STATUS_CODE_SESSION_TIMEOUT);
            }
            $session = SessionManagement::getSession();
        }
    }
}
?>