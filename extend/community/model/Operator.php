<?php
/**
 * 运营商模型
 */
namespace  community\model;

use think\Model;
use core\exception\CommonException;

class Operator extends Model
{

    protected $table = "qis_enterprise";
    
    public function isPersist()
    {
        return !!$this->getAttr("id");
    }
    
    /**
     * 获取token串
     */
    public  function getToken()
    {
        if($this->getAttr("token")){
           return  $this->getAttr("token");
        }
        $token = $this->build_token();
        $this->token = $token;
        return $token;
    }
    /**
     * 创建token hash
     * @return unknown
     */
    protected function build_token()
    {
        $eid = $this->getAttr("ep_id");
        $maintenance = $this->getAttr("maintenance");
        $salt = rand(1000, 9999);
        $token = md5($salt.$eid.$maintenance);
        return $token;
    }
    
    /**
     * 模型是否相同
     * @param Model $model
     */
    public  function equals(Model $model)
    {
       if($model instanceof Operator)
       {
           return ($model->getAttr("id") == $this->getAttr("id"));
       }
       return false;
    }
    
    /**
     * 是否受限
     */
    public function isLimited()
    {
        return false;
    }
    
    /**
     * 判断状态
     * @param type $data
     * @return $status
     */
    public function checkMaintenance()
    {
        
    }
    /**
     * 注册/修改企业
     * @param string $key
     * @return multitype:
     */
    public function register()
    {
        if(empty($this->getAttr('ep_id')) ||empty($this->getAttr('maintenance'))) {
            throw new CommonException("非法操作",ILLEGAL_OPRATION);
        }
        $mtAddr = $this->getMaintenance();
        $provinceServer = new Maintenance();
        $map['s_domain'] = [ 'like', '%'.$mtAddr.'%'];
        $maintenance = $provinceServer->where($map)->find();
        //$maintenance = Maintenance::get(["extranet" => $this->getMaintenance()]);
        if(empty($maintenance)) {
            throw new CommonException("不存在该运维地址",MAINTENANCE_NOT_EXIST);
        }
        //检测企业是否存在
        if(!$maintenance->HttpCheck($this)) {
            throw new CommonException("不存在该企业信息",ENTERPRISE_NOT_EXIST);
        }
        //查找现有的
        $where = ["ep_id"=>$this->getAttr('ep_id'),"maintenance"=>$this->getAttr("maintenance")];
        $this->isUpdate && $where['id'] = ['<>',$this->getAttr('id')];
        $ep = Operator::get($where);
        if(!empty($ep)) {
            throw new CommonException("该企业已经注册过了",ENTERPRISE_BE_REGISTERED);
        }
        //插入数据
        empty($this->isUpdate) && $this->setAttr('token', $this->build_token()) && $this->setAttr('create_time', NOW_TIME);
        $result = $this->save();
        if (false === $result) {
            throw new CommonException('企业注册失败', ENTERPRISE_REGISTER_FAILED);
        }
        return $result;
    }
    /**
     * 注册/修改企业
     * @param string $key
     * @return multitype:
     */
    public function modifyEp()
    {
        if(empty($this->getAttr('ep_id')) ||empty($this->getAttr('maintenance'))) {
            throw new CommonException("非法操作",ILLEGAL_OPRATION);
        }
        
        $maintenance = Maintenance::get(["extranet" => $this->getMaintenance()]);
        if(empty($maintenance)) {
            throw new CommonException("不存在该运维地址",MAINTENANCE_NOT_EXIST);
        }
        //检测企业是否存在
        if(!$maintenance->HttpCheck($this)) {
            throw new CommonException("不存在该企业信息",ENTERPRISE_NOT_EXIST);
        }
        //插入数据
        empty($this->isUpdate) && $this->setAttr('token', $this->build_token()) && $this->setAttr('create_time', NOW_TIME);
        $result = $this->save();
        if (false === $result) {
            throw new CommonException('企业修改失败', ENTERPRISE_REGISTER_FAILED);
        }
        return $result;
    }
    
    protected function getMaintenance(){
        $rdata = str_replace(["http://","https://"], "", $this->getData("maintenance"));
        $rdata = substr($rdata, 0,strpos($rdata, ":"));
        return $rdata;
    }

    /**
     * 撤销企业注册
     */
    public  function unregister()
    {
        if (!$this->id) {
            ExLog::log("不存在该企业", ExLog::DEBUG);
        }
        // 删除任务
        // 清除 质检文件
        // 关闭当前运行的进程
        // 删除库
        
        ExLog::log("qis_config删除企业配置:" . $this->getAttr("id") );
        SystemConfig::destroy(["ep_id"=>$this->getAttr("id")]);
        ExLog::log("qis_ep删除企业信息:" . $this->getAttr("id") );
        $this -> delete();
        $cmdLine = sprintf("php " . SERVER_ROOT . "think epm delete {$this->id}");
        $comand = new CliExcutor();
        ExLog::log("后台删除数据:" . $cmdLine);
        $res = $comand->excuteCmd($cmdLine);
        if (isset($res["result"][0]) && $res["result"][0] !== "success") {
            ExLog::log("删除企业DB库:" . $cmdLine . " 执行失败！");
            throw new CommonException('删除企业DB失败', QIS_DELETE_EP_DB_FAILED);
        }
    }
    
    
    /**
     * 关停服务
     */
    public function shutdown() 
    {
        $this->save(["status"=>-1]);
        ExLog::log("系统关闭企业".$this->getAttr("name"));
        SyncService::singleton()->Sync($this,2);
    }
    /**
     * 启动服务
     */
    public function start()
    {
        $this->save(["status"=>2]);
        ExLog::log("开启企业服务".$this->getAttr("name"));
        SyncService::singleton()->Sync($this,2);
    }
    
    public function config()
    {
        return $this->hasMany('SystemConfig','ep_id');
    }
}
