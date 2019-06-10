<?php 
/*
 +-------------------------------------------------------------------------------------------
 + Title        : 用户管理
 + Version      : V1.0.0.2
 + Initial-Time : 2018年11月09日
 + @auth Dream <1015617245@qq.com>
 + Last-time    : 2018-11-09
 + Desc         : 用户处理
 +-------------------------------------------------------------------------------------------
*/

namespace app\bladmin\controller;

use authority\logic\UserLogic;
use core\controller\Admin;
use core\controller\tool\ApiPagination;

class User extends Admin
{
    use ApiPagination;

    /**
     * @var UserLogic
     */
    protected $logic;

    public function _initialize()
    {
        $this->logic = UserLogic::singleton();
        parent::_initialize();
    }

    public function getUsers()
    {
        $params = $this->paginationParams();
        $result = $this->logic->getUsersList($params);
        $this->result($result);
    }

    /**
     * 添加用户
     * @throws \authority\exception\UserException
     */
    public function addUser()
    {
        $this->checkRequest();
        $flag = $this->logic->addUserByData($this->request->param());
        if($flag){
            $this->result_success();
        }
        $this->result("",USER_ADD_FAILED);
    }

    /**
     * 保存用户
     */
    public function modifyUser()
    {
        $this->checkRequest();
        $flag = $this->logic->modifyUserByData($this->request->param());
        if($flag){
            $this->result_success();
        }
        $this->result("",USER_SAVE_FAILED);
    }

    /**
     * 禁用用户
     */
    public function disableUser()
    {

    }
    /**
     * 启用用户
     */
    public function enableUser()
    {

    }

    /**
     * 删除用户
     */
    public function deleteUser()
    {
        $result = $this->logic->deleteUserByIds($this->request->param("uids"));

    }



}
