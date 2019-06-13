<?php
/*
 +-------------------------------------------------------------------------------------------
 + Title        : 权限管理
 + Version      : V1.0.0.2
 + Initial-Time : 2018年11月09日
 + @auth        ： Dream  <1015617245@qq.com>
 + Last-time    : 2018-11-09
 + Desc         : 权限管理
 +-------------------------------------------------------------------------------------------
*/
namespace authority\service;

use authority\model\AuthAction;
use authority\model\AuthRule;
use core\model\Role;
use core\service\Service;

class RuleService extends Service implements  RuleManagement
{
    /**
     * @var AuthRule
     */
    protected $ruleMode ;

    protected function __construct()
    {
        $this->ruleMode = new AuthRule();
        parent::__construct();
    }

    /**
     * 查询列表
     * @param array $condition
     * @return array|mixed
     */
    public function getRules($condition=[])
    {
        return $this->ruleMode->searchInstances($condition);
    }

    public function bindRole(AuthRule $rule, Role $role)
    {
        // TODO: Implement bindRole() method.
    }

    public function unbindRole(AuthRule $rule, Role $role)
    {
        // TODO: Implement unbindRole() method.
    }

    public function hasAction(AuthRule $rule,AuthAction $action)
    {

    }
}

