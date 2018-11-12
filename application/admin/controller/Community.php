<?php
/**
 * 系统管理员
 * 社区管理
 */
namespace app\admin\controller;

use core\controller\Admin;
use think\Request;
use core\controller\tool\ApiPagination;
use community\service\CommunityService;
use app\admin\validate\BlAdminValidate;
use community\model\Community as CommunityModel;

class Community extends Admin
{
    use ApiPagination;

    protected $validate = BlAdminValidate::class;

    protected $communityService = null;

    public function _initialize()
    {
        $this->communityService = CommunityService::singleton();
        parent::_initialize();
    }

    /**
     * 获取列表
     *
     * @author Dream
     */
    public function getCommunity()
    {
        $communities = $this->communityService->searchInstances($this->paginationParams());
        $this->result($communities);
    }

    /**
     * 获取社区基本信息
     *
     * @author Dream
     */
    public function getCommunityInfo()
    {
        $this->checkRequest();
        $cid = $this->request->param("cid");
        $community = CommunityModel::get($cid);
        // $community = $this->communityService->searchInstances(["id"=>$cid]);
        if (empty($community)) {
            $this->result("", STATUS_CODE_COMMUNITY_NOT_FOUND);
        }
        $this->result($community);
    }

    /**
     * 注册社区
     * 添加到数据库
     */
    public function regiter()
    {
        $this->checkRequest();
        $from_data = [];
        $from_data["cname"] = $this->request->param("cname");
        $from_data["address"] = $this->request->param("address");
        $from_data["province_id"] = $this->request->param("province_id");
        $from_data["city_id"] = $this->request->param("city_id");
        $from_data["logo"] = $this->request->param("logo");
        $from_data["slogan"] = $this->request->param("slogan");
        $community = $this->communityService->createCommunity($from_data);
        $this->log("注册新的社区");
        $this->result("");
    }

    /**
     * 注销
     * 
     * @return mixed|string
     */
    public function unRegister()
    {
        // 暂不支持
        $this->result("", STATUS_CODE_NOT_SUPPORT);
    }
}
