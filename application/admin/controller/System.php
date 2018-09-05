<?php
/**
 * 系统管理员
 * 管理管理
 * @author Dream<1015617245@qq.com>
 */
namespace app\admin\controller;

use core\controller\Admin;
use think\Request;
use core\models\SystemConfig;
use core\utils\ExLog;
use core\models\SystemLog;

class System extends  Admin
{

    /**
     * 系统设置 列表
     * 
     */
    public function Setting()
    {
        $keywords = trim($this->request->param("search"));
        if(!empty($keywords)){
            $configs = SystemConfig::where('key|value|describe','like','%'.$keywords."%");
        }else{
            $configs = SystemConfig::where(1);
        }
        $page = SystemConfig::getValue("list_page_size")?:10;
        $configs = $configs->where(["gid"=>SystemConfig::SYSTEM_DEFAULT_GID])->order("id","desc")->paginate($page,false,[
            'type'     => 'Bootstrap4',
            'var_page' => 'page',
            'query' => request()->param(),
        ]);
        $this->assign("configs",$configs);
        return $this->fetch();
    }
    
    /**
     * 获取setting详情
     */
    public function getSetting()
    {
        $key = $this->request->param("s_key");
        if(empty($key)){
            ExLog::log("该配置不存在",ExLog::INFO);
            $this->result("", ILLEGAL_OPRATION, "该配置不存在");
        }
        $info = SystemConfig::getValue($key,SystemConfig::SYSTEM_DEFAULT_GID,true);
        if(empty($info)){
            ExLog::log("该配置不存在",ExLog::INFO);
            $this->result("", ILLEGAL_OPRATION, "该配置不存在");
        }
        $this->result($info);
    }

    /**
     * 添加配置项
     * 可以修改配置
     */
    public function addSetting()
    {
        $key = $this->request->param("s_key");
        $value = $this->request->param("s_value");
        $describe = $this->request->param("s_describe");
        $status = $this->request->param("s_status");
        $ret = SystemConfig::getValue($key, SystemConfig::SYSTEM_DEFAULT_GID, true);
        if (! empty($ret)) {
            ExLog::log("该配置已经存在",ExLog::INFO);
            $this->result("", ILLEGAL_OPRATION, "该配置已经存在");
        }
        $result = SystemConfig::setValue($key, $value, SystemConfig::SYSTEM_DEFAULT_GID, $describe);
        if (! empty($result)) {
            $this->log("添加配置项:[".$key."]");
            $this->result("success");
        } else {
            $this->result("", ILLEGAL_OPRATION, "该配置无法操作");
        }
    }
    
    /**
     * 删除设置
     */
    public function delSetting()
    {
        $key = $this->request->param("key");
        $ret = SystemConfig::getValue($key, SystemConfig::SYSTEM_DEFAULT_GID, true);
        if(empty($ret) ||$ret['type'] = 0)
        {
            $this->result("", ILLEGAL_OPRATION, "该配置无法删除");
        }
        //删除配置
        SystemConfig::destroy([
            'gid' => SystemConfig::SYSTEM_DEFAULT_GID,
            'key' => $key
        ]);
        $this->log("删除配置项:[".$key."]",ExLog::INFO);
        $this->result("success");
    }
    /**
     * 编辑配置项
     * 
     */
    public function editSetting()
    {
        $key = $this->request->param("s_key");
        $value = $this->request->param("s_value");
        $describe = $this->request->param("s_describe");
        $status = $this->request->param("s_status");
        $ret = SystemConfig::getValue($key, SystemConfig::SYSTEM_DEFAULT_GID, true);
        if (empty($ret)) {
            ExLog::log("该配置不存在",ExLog::INFO);
            $this->result("", ILLEGAL_OPRATION, "该配置不存在");
        }
        $result = SystemConfig::setValue($key, $value, SystemConfig::SYSTEM_DEFAULT_GID, $describe);
        if (! empty($result)) {
            $this->log("修改配置项:[".$key."]");
            $this->result("success");
        } else {
            $this->result("", ILLEGAL_OPRATION, "该配置无法操作");
        }
    }
    
    /********************************
     ************ 日志管理***********
     *******************************/
    public function Logm(){
        $keywords = trim($this->request->param("search"));
        if(!empty($keywords)){
            $logs = SystemLog::where('message|params|operator_url','like','%'.$keywords."%");
        }else{
            $logs = SystemLog::where(1);
        }
        $page = 20;//SystemConfig::getValue("list_page_size")?:20;
        $logs = $logs->where(["gid"=>SystemConfig::SYSTEM_DEFAULT_GID])->order("id","desc")->paginate($page,false,[
            'type'     => 'Bootstrap4',
            'var_page' => 'page',
            'query' => request()->param(),
        ]);
        $this->assign("logs",$logs);
        return $this->fetch();
    }
    /**
     * 导出日志
     */
    public function  exportLog()
    {
        
    }
    
   
    /**
     * 系统功能列表
     * 默认用户反馈
     */
    public function Manage()
    {
        //$comments = Comments::all();
        return $this->fetch();
    }
    
    /**
     * 用户反馈列表
     */
    public function feedback()
    {
        sleep(3);
        return $this->fetch();
    }
    
    /**
     * 用户反馈列表
     */
    public function comment()
    {
        return $this->fetch();
    }
    
    
    /**
     * 升级系统版本
     */
    public function sysUpdate()
    {
        sleep(5);
        return $this->fetch();
    }
    /**
     * 监控
     */
    public function Monitor()
    {
        return $this->fetch();
    }
    
    
    /**
     * 数据备份
     */
    public function  Backup()
    {
        return $this->fetch();
    }
    
    
    
}
