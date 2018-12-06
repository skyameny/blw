<?php
/**
 * 系统管理员
 * 管理管理
 * @author Dream<1015617245@qq.com>
 */
namespace app\admin\controller;

use core\controller\Admin;
use core\controller\tool\ApiPagination;
use core\model\SystemConfig;
use core\service\SystemService;

class System extends  Admin
{
    use ApiPagination;
    
    protected $sysService = null;
    
    public function _initialize(){
        $this->sysService = SystemService::singleton();
        parent::_initialize();
    }
    
    /********************************
     ************ 日志管理***********
     *******************************/
    /**
     * 获取日志列表 
     * 支持uid ip level gid 和keywords查询
     */
    public function getLog(){
        $condition = [];
        if($this->request->has("uid")){
            $condition["account"] = $this->request->param("uid");
        }
        if($this->request->has("ip")){
            $condition["operator_ip"] = $this->request->param("ip");
        }
        if($this->request->has("level")){
            $condition["level"] = $this->request->param("level");
        }
        $condition = array_merge($condition,$this->paginationParams());
        $logs = $this->sysService->getLogInstance($condition);
        $this->result($logs);
    }
    /**
     * 导出日志
     */
    public function  exportLog()
    {
        
        
    }
    
    /**
     * 清理日志
     * 此项功能由定时器任务完成
     * 这里仅仅作为运维接口  仅支持一个参数 before_time
     * 不建议使用
     */
    public function clearLog()
    {
        $beforeTime  = $this->request->param("before_time"); 
        if(!empty($beforeTime)){
            $result = $this->sysService->deleteLogs($beforeTime);
            if($result>0){
                $this->log("删除系统日志");
            }
            $this->result("");
        }
        $this->result("",STATUS_CODE_PARAM_ERROR);
    }
    
    /**
     * 升级系统版本
     */
    public function getVersion()
    {
       $returnValue = $this->sysService->getVersion();
       $this->result($returnValue);
    }
    /**
     * 监控
     */
    public function Monitor()
    {
        $this->result("",STATUS_CODE_NOT_SUPPORT);
    }
    
    /**
     * 数据备份
     */
    public function  Backup()
    {
        $this->result("",STATUS_CODE_NOT_SUPPORT);
    }
    
}
