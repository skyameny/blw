<?php
namespace core\includes\session;

/**
 * Session 接口
 * 用于获取用户登录信息
 * @author keepwin100 <1015617245@qq.com>
 */

interface Session
{

    /**
     * 获取用户
     * 
     * @access public
     * @author keepwin100
     * @return core\includes\user\User
     */
    public function getUser();
    /**
     * 获取用户ID
     *
     * @access public
     * @author keepwin100
     * @return \User
     */
    public function getUserId();
    
    
    /**
     * 获取用户所在社区
     *
     * @access public
     * @author keepwin100
     * @return string
     */
    public function getGarden();

    /**
     * 获取用户标签
     *
     * @access public
     * @author keepwin100
     * @return string
     */
    public function getUserLabel();
    
    /**
     * 获取用户角色
     *
     * @access public
     * @author keepwin100
     * @return array An array of strings
     */
    public function getUserRoles();

    /**
     * 返回用户设置语言
     *
     * @access public
     * @author keepwin100
     * @return string
     */
    public function getDataLanguage();
    
    /**
     * 默认语言
     *
     * @access public
     * @author keepwin100
     * @return string
     */
    public function getInterfaceLanguage();
    
    /**
     * 时区
     *
     * @access public
     * @author keepwin100
     * @return string
     */
    public function getTimeZone();
    
    /**
     * 获取用户属性
     * 
     * @param string $property
     * @return array
     */
    public function getUserPropertyValues($property);
    
    /**
     * 更新session
     */
    public function refresh();
    
}