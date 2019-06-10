<?php
namespace core\model;

use core\model\BlModel;
use core\utils\ExLog;

/**
 * 同一IP限制
 * 
 * @author Dream<Hukaijun@emicnet.com>
 * 
 * @create time 2018年11月13日16:29:45
 * 
 * @desc 解决河北用户名枚举系统安全隐患 
 *       在Login的接口处使用 IptemModel::auth($ip);
 *       登录成功后使用IptemModel::clear($ip);
 *       
 * @access 本类没有外界配置可用  需要安装Emic扩展
 */
defined("NOW_TIME") or define("NOW_TIME", time());

class Iptem extends BlModel
{

    // 防止刷新时间间隔 秒
    const ALLOW_DURATION_TIMES = 5;

    // 检查时间段 秒
    const CHECK_DURATION_TIME = 600;

    // 允许尝试次数 次
    const ALLOW_TRY_NUM = 10;

    // 禁止时间 自然天
    const FORBIDDEN_DAYS = 1;

    // ip状态 不可用
    const STATUS_DISABLE = 1;

    // ip状态 可用
    const STATUS_ENABLE = 0;

    // 最后一次登录
    public $last_login_time = 0;

    // 当天记录
    public $records = array();

    // 当前监测时段记录
    public $duration_records = array();

    public $status = 0;

    public $ip = ""; 
    /**
     * 验证ip的有效性
     */
    public static function Auth($ip,$duration=0)
    {
        
        $iptem = self::get(array(
            "ip" => $ip
        ));
        if (! is_null($iptem)) {
            //初始化数据
            $iptem->ip = $ip;
            $iptem->initH();
            
            if ($iptem->status == self::STATUS_DISABLE) {
                ExLog::log("系统将限制IP:${ip},该IP尝试了太多次请求", ExLog::DEBUG);
                throw new \Exception("request_auth_failed");
                // return false;
            }
            //$allow_duration_times = config("ALLOW_DURATION_TIMES") ? config("ALLOW_DURATION_TIMES") : self::ALLOW_DURATION_TIMES;
            if (! empty($duration) && (NOW_TIME - $iptem->last_login_time) < $duration) {
                ExLog::log("系统将限制IP:${ip},时间间隔太短", ExLog::DEBUG);
                throw new \Exception("operation_too_frequent");
                // return false;
            }
        } else {
            $iptem = new self();
            $iptem->ip = $ip;
        }
        $iptem->logRecord();
        return true;
    }

        // 验证成功才可以记录
    protected function logRecord()
    {
        $data = array();
        $data["ip"] = $this->ip;
        // $data["status"] = $this->status;
        $data["record"] = json_encode(array_merge($this->records, array(NOW_TIME)));
        
        if (isset($this->id) && ! empty($this->id)) {
            $this->save($data);
        } else {
            Iptem::create($data);
        }
        ExLog::log("保存到数据库：" . self::getLastSql(), ExLog::DEBUG);
    }
    
    /**
     * 设置状态
     * 
     * @param unknown $status
     * @return number|\think\false
     */
    protected function setStatus($status)
    {
        return $this->save(array("status"=>$status));
    }
    
    /**
     * 登录成功
     * 需要清理尝试记录
     *
     * @param unknown $ip            
     * @return boolean
     */
    public static function clear($ip)
    {
        $model = new self();
        $model->where(array(
            "ip" => $ip
        ))->delete();
        ExLog::log("正在开放IP:${ip}的限制", ExLog::DEBUG);
    }

    /**
     * 初始化 历史数据
     *
     * @param unknown $iptem            
     * @return array
     */
    protected function initH()
    {
        $returnValue = array();
        $iptem = $this->toArray();
        $this->id = $iptem["id"];
        $records = json_decode($iptem["record"], true);
        $today_time = strtotime(date("Y-m-d", NOW_TIME));
        //
        foreach ($records as $key => $record) {
            if (($today_time - $record) > 0) {
                // 移除今天之前的数据
                unset($records[$key]);
                continue;
            }
            if ((NOW_TIME - $record) < self::CHECK_DURATION_TIME) {
                $this->duration_records[] = $record;
            }
        }
        $this->status = $iptem["status"];
        $this->records = $records;
        $this->last_login_time = max($this->records);
        
        // 每天第一次会初始化状态
        if (count($this->records) == 0) {
            $this->status = self::STATUS_ENABLE;
            $this->setStatus(self::STATUS_ENABLE);
        }
        if(count($this->duration_records) >= self::ALLOW_TRY_NUM)
        {
            $this->status = self::STATUS_DISABLE;
            $this->setStatus(self::STATUS_DISABLE);
        }
    }
}

?>