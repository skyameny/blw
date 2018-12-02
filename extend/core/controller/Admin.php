<?php
/**
 * 系统基础控制器
 */
namespace core\controller;

use think\Controller;
use core\controller\tool\Authentication;
use core\includes\session\SessionManagement;
use core\model\User;

class Admin extends Frame
{
    use Authentication;

    /**
     * 模板主题设置
     * 
     * @access protected
     * @param string $theme
     *            模版主题
     * @return Action
     */
    protected function theme($theme)
    {
        $this->theme = $theme;
        return $this;
    }

    /**
     * 初始化
     * 
     * {@inheritdoc}
     *
     * @see \core\controller\Frame::_initialize()
     */
    public function _initialize()
    {
        parent::_initialize();
        // 验证用户是否是超级管理员
        $user_type = SessionManagement::getSession()->getUserPropertyValues("type");
        if ($user_type !== User::ADMIN_USER_TYPE) {
            $this->result("", STATUS_CODE_AUTH_FAILED);
        }
        
        if (! $this->verification()) {
            $this->result("", STATUS_CODE_PERMISSION_DEND);
        }
    }

    /**
     * 设置活动的nav
     *
     * @param unknown $menus            
     */
    protected function setActive(&$menus)
    {
        $url = $this->request->module() . "/" . $this->request->controller() . "/" . $this->request->action();
        $url = str_replace("/index", "", strtolower($url));
        foreach ($menus as $key => $menu) {
            if (stripos($url, $menu["name"]) !== false) {
                $menus[$key]["active"] = 1;
                if (! empty($menu["children"])) {
                    foreach ($menu["children"] as $key2 => $cmenu) {
                        if (stripos($url, $cmenu["name"]) !== false) {
                            $menus[$key]["children"][$key2]["active"] = 1;
                        }
                    }
                }
                break;
            }
        }
    }
}
?>