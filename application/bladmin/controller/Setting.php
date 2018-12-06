<?php
/**
 * 系统->设置
 * /bladmin/setting/config
 */
namespace app\admin\controller;

use core\controller\Admin;
use core\service\SettingService;
use core\controller\tool\ApiPagination;

class Setting extends Admin
    {
    use ApiPagination;

    protected $settingService = null;

    public function _initialize()
    {
    $this->settingService = SettingService::singleton();
    parent::_initialize();
    }

    /**
     * 获取系统设置
     */
    public function getConfig()
    {
        $configs = $this->settingService->searchInstances($this->paginationParams());
        foreach ($configs["content"] as $_key => $config) {
            $configs["content"][$_key] = $config->visible(["key", "value"])->toArray();
        }
        $this->result($configs);
    }
    
    /**
     * 设置
     * key 配置键
     * value 配置值
     * gid 社区id
     * describe 描述
     */
    public function setConfig()
    {
      //  $this->checkParams("BlAdminValidate");
        $key = $this->request->param("key");
        $value = $this->request->param("value");
        $gid = $this->request->param("gid")??0;
        $describe = $this->request->param("describe")??"";
        $this->settingService->setValue($key, $value,$gid,$describe);
        $this->log("设置配置[$key]=>[$value]");
        $this->result("");
    }

    /**
     * 删除配置
     */
    public function deleteConfig()
    {
        //$this->checkParams("BlAdminValidate");
        $key = $this->request->param("key");
        $this->settingService->deleteConfig($key);
        $this->log("删除配置[$key]");
        $this->result("");
    }
}
