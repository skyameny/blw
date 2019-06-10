<?php
/**
 * 验证器
 * 用于
 * User: keepwin100
 * Date: 2019-04-23
 * Time: 19:48
 */
namespace authority\traits;

use authority\service\AuthorityService;
use core\includes\session\SessionManagement;
use core\includes\user\AnonymousUser;
use think\Request;

trait Authentication
{
    public function verification()
    {
        $result = false;
        $params = Request::instance()->param();
        foreach (SessionManagement::getSession()->getUserRoles() as $role) {
            $result = AuthorityService::authentication($role, $this->buildAction(), $params);
            if ($result) {
                return true;
            }
        }
        return $result;
    }

    private function buildAction()
    {
        $request = Request::instance();
        $returnValue = $request->module().DIRECTORY_SEPARATOR. $request->controller().
            DIRECTORY_SEPARATOR.$request->action();
        return $returnValue;
    }

}
