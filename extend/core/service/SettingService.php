<?php
/**
 * 设置服务
 */
namespace core\service;

use core\model\SystemConfig;
use core\utils\ExLog;
use core\exception\CommonException;
use think\Config;

class SettingService extends Service
{
    const AVAILABLE_USED = 1; //可用的type值
    
    protected  static  $sys_config = [];

    /**
     * 获取配置项 
     * 
     * @param string $key
     * @param number $eid
     * @param string $full
     * @return unknown
     */
    public static function getValue($key = "", $eid = 0, $full = false)
    {
        $returnValue = null;
        
        $systemConfig = new SystemConfig();
        if (! empty($key)) {
            $map['key'] = $key;
        }
        $map['gid'] = ! empty($eid) ? $eid : 'null';
        $map["status"] = self::AVAILABLE_USED;
        $configs = systemConfig::all($map);
        if (count($configs) == 1) {
            $config = $configs[0];
            if ($full) {
                return $config->toArray();
            }
            $returnValue = $config->getData("value");
        } else {
            foreach ($configs as $config) {
                if ($full) {
                    $returnValue[] = $config->toArray();
                } else {
                    $returnValue[$config->getAttr("key")] = $config->getAttr("value");
                }
            }
        }
        return $returnValue;
    }

    /**
     * 批量设置配置
     *
     * @param unknown $data            
     * @param unknown $eid            
     * @return boolean
     */
    public  function setValues($data, $eid = 0)
    {
        if (! is_array($data)) {
            return false;
        }
        foreach ($data as $item) {
            if (! is_array($item)) {
                return false;
            }
            $_key = isset($item["key"])?$item["key"]:"";
            $_value = isset($item["value"])?$item["value"]:"";
            $_describe = isset($item["describe"])?$item["describe"]:"";
            if (! $this->setValue($_key, $_value, $eid,$_describe)) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * 设置配置项
     * @param unknown $key
     * @param unknown $value
     * @param number $eid
     * @param string $describe
     * @return boolean
     */
    public  function setValue($key,$value,$eid=0,$describe="",$type =2)
    {
        $data = [];
        $result = [];
        if (empty($key)) {
            return false;
        }
        if (! preg_match('#^[a-zA-Z_]{1}[a-zA-Z\d_]{3,32}$#', $key)) {
            ExLog::log("配置项[".$key."]不符合规范错误");
            throw new CommonException("",STATUS_CODE_SETTING_NAME_ERROR);
            //return false;
        }
        $config = SystemConfig::get([
            "key" => $key,
            "gid" => $eid
        ]);
        if(!is_null($config) && $config->isReadOnly()){
            throw new CommonException("",STATUS_CODE_SETTING_READONLY);
        }
        $save_data = [
            'key' => $key,
            'value' => $value,
            'gid' =>$eid,
            'type' =>($config)?$config->getAttr("type"):$type,
            'status' => self::AVAILABLE_USED
        ];
        if(!empty($describe)){
            $save_data['describe'] = $describe;
        }
        
        if (! is_null($config)) {
            $result = $config->isUpdate(true)->save($save_data);
        } else {
            $config = new SystemConfig();
            $config->data($save_data);
            $result = $config->save();
        }
        $this->_apply($key);
        return $result;
    }
    
    /**
     * 应用配置 立即生效
     * @param string $key
     */
    protected function _apply($key="")
    {
        //
    }
    
//     初始化
    
    protected function _init(){
        self::$sys_config = self::getValue();
    }

    /**
     * 查询系统当前所有的配置
     * 
     * @param array $condition
     * @return boolean|\think\static[]|\think\false
     */
    public function searchInstances($condition = [])
    {
        $sc_model = new SystemConfig();
        if(!isset($condition["cid"])){
            $condition["cid"] = SYSTEM_DEFAULT_CID;
        }
        return $sc_model->searchInstances($condition);
    }

    /**
     * 删除配置
     * @param string $key
     * @param number $gid
     * @throws CommonException
     */
    public function deleteConfig($key, $gid = 0)
    {
        $config = SystemConfig::get([
            "key" => $key
        ]);
        if(is_null($config)){
            throw new CommonException("",STATUS_CODE_SETTING_NOT_EXISTS);
        }
        if (! $config->isDeleteAble()) {
            throw new CommonException("",STATUS_CODE_SETTING_NOT_DELETEABLE);
        }
        return $config->delete();
    }

}
