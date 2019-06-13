<?php 
/*
 +-------------------------------------------------------------------------------------------
 + Title        : 权限管理
 + Version      : V1.0.0.2
 + Initial-Time : 2018年11月09日
 + @auth Dream <1015617245@qq.com>
 + Last-time    : 2018-11-09
 + Desc         : 权限处理
 +-------------------------------------------------------------------------------------------
*/

namespace app\bladmin\controller;

use app\bladmin\validate\UserAdminValidate;
use authority\logic\UserLogic;
use core\controller\Admin;
use core\controller\tool\ApiPagination;

class Rule extends Admin
{
    use ApiPagination;

    /**
     * @var UserLogic
     */
    protected $logic;

    protected $validate = UserAdminValidate::class;

    public function _initialize()
    {
        $this->logic = UserLogic::singleton();
        parent::_initialize();
    }

    public function getRule()
    {
        $params = $this->paginationParams();
        $result = $this->logic->getUsersList($params);
        $this->result($result);
    }

    /**
     * 添加用户
     * @throws \authority\exception\UserException
     * @param
     *
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
        $this->logic->modifyUserByData($this->request->param());
        $this->result_success();
    }

    /**
     * 禁用用户
     */
    public function disableUser()
    {
        $uid = $this->request->param("uid");
        $flag = $this->logic->disAbleUserById($uid);
        if($flag){
            $this->result_success();
        }
        $this->result("",STATUS_CODE_USER_SAVE_FAILED);
    }
    /**
     * 启用用户
     */
    public function enableUser()
    {
        $uid = $this->request->param("uid");
        $flag = $this->logic->enAbleUserById($uid);
        if($flag){
            $this->result_success();
        }
        $this->result("",STATUS_CODE_USER_SAVE_FAILED);
    }

    /**
     * 删除用户
     */
    public function deleteUser()
    {
        $params = $this->request->param();
        $uids = $params["uids"];
        $result = $this->logic->deleteUserByIds($uids);
        $this->result($result);
    }
}
