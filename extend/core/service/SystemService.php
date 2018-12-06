<?php 
namespace core\service;

use think\Request;
use think\Db;
use core\utils\ExLog;
use core\model\OperateLog;
use core\exception\CommonException;

class SystemService extends Service
{
    protected static $instance=null;
    
    /**
     * 记录到系统日志
     * @param unknown $eid
     * @param unknown $uid
     * @param unknown $message
     * @param string $level
     */
    public function log($garden,$user,$message,$level="1")
    {
         //$db = helpersEnterprise::getEnterpriseDb($bladmin->getAttr("eid"));
         $r_data = [];
         $r_data["gid"] = (is_null($garden))?0:$garden->getAttr("id");
         $r_data["account"] = $user->getAttr("id");
         $r_data["operator_ip"] = Request::instance()->ip();
         $r_data["message"] = $message;
         $r_data["operator_url"] = Request::instance()->baseUrl();
         $r_data["create_time"] = NOW_TIME;
         $r_data["level"] = intval($level);
         //屏蔽密码
         $params = Request::instance()->param();
         if(isset($params["passwd"])){
             $params["passwd"] = "******";
         }
         $r_data["params"] = empty($params)?"":json_encode($params);
         $log_id =Db::name("log")->insertGetId($r_data);
         if($log_id){
             return true;
         }
         ExLog::log("写入失败",serialize($r_data));
    }
    
    /**
     * 获取日志列表
     * @param array $condition
     * @return boolean|\think\static[]|\think\false
     */
    public function getLogInstance($condition =[])
    {
        $sc_model = new OperateLog();
        if(!isset($condition["gid"])){
            $condition["gid"] = SYSTEM_DEFAULT_GID;
        }
        return $sc_model->searchInstances($condition);
    }

    /**
     * 删除日志
     * 
     * @param unknown $before_time            
     * @throws CommonException
     * @return number
     */
    public function deleteLogs($before_time)
    {
        $life_time = SettingService::singleton()->getValue("log_max_time");
        if (NOW_TIME - $before_time < $life_time) {
            throw new CommonException("系统日志至少保留${life_time}秒", STATUS_CODE_ILLEGAL_OPRATION);
        }
        ;
        $delwhere["create_time"] = ["<",$before_time];
        $result = Db::name("log")->where($delwhere)->delete();
        ExLog::log("清理日志：" . Db::getLastSql(), Exlog::DEBUG);
        return $result;
    }
    
    /**
     * 获取系统版本
     */
    public function getVersion()
    {
        return array(
            "core"=>BL_CORE_VERSION,
            "community" =>BL_COMMUNITY_VERSION
        );
    }
    
    
    
    
    
}


?>