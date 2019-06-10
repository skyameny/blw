<?php
/**
 * 权限规则管理
 * User: keepwin100
 * Date: 2019-04-24
 * Time: 13:59
 */
namespace  authority\service;

use core\model\Role;
use authority\model\AuthRule;

interface RuleManagement
{
    /**
     * 给指定角色授权
     * 该方法能自动添加当前rule的子节点
     * @param AuthRule $rule
     * @param $role
     * @return mixed
     */
    public function addRuleForRole(AuthRule $rule,Role $role);
    /**
     * 移除指定角色授权
     * 该方法能自动移除当前rule的子节点
     * @param AuthRule $rule
     * @param $role
     * @return mixed
     */
    public function removeRuleForRole(AuthRule $rule,Role $role);

    /**
     * 获取角色的所有的规则
     * 是否限制隐藏的
     * @param Role $role
     * @param bool $show_disable
     * @return mixed
     */
    public function getAllRule(Role $role,$show_disable=false);

    /**
     * 判断是否具有该权限
     * @param Role $role
     * @param AuthRule $rule
     * @return mixed
     */
    public function hasRule(Role $role,AuthRule $rule);

    /**
     * 绑定到角色
     * @param AuthRule $rule
     * @param Role $role
     * @return mixed
     */
    public function bindRole(AuthRule $rule,Role $role);

    /**
     * 解除绑定
     * @param AuthRule $rule
     * @param Role $role
     * @return mixed
     */
    public function unbindRole(AuthRule $rule,Role $role);

    #以下接口作为备用管理接口 目前不需要实现
    /**
     * 给指定的rule绑定action
     * @param $rule
     * @param array $actions
     * @return mixed
     */
    #public function bindAction($rule,$actions);

    /**
     * 解除绑定rule绑定action
     * @param $rule
     * @param $actions
     * @return mixed
     */
    #public function unbindAction($rule,$actions);

    /**
     * 添加Action
     * @return mixed
     */
    #public function addAction();
}