<?php
/**
 * 对象存储配置模型
 * @date: 2018年8月17日
 * @update: Dream
 */
namespace core\model;

use core\utils\ExLog;

class SystemConfig extends BlModel
{
    const AVAILABLED_USED = 1; //可用的type值

    const SYSTEM_DEFAULT_EID = 0; //默认系统配置gid

    protected $name = 'config';

    protected  static  $sys_config = [];

    /**
     * 获取配置项
     *
     * @param string $key
     * @param number $eid
     * @param string $full
     * @return mixed
     */
    public static function getValue($key = "", $cid = 0, $full = false)
    {
        $returnValue = null;
        if (! empty($key)) {
            $map['key'] = $key;
        }
        $map['cid'] = ! empty($cid) ? $cid : 'null';
        $map["status"] = self::AVAILABLED_USED;
        $configs = SystemConfig::all($map);
        if (count($configs) == 1 && $key != "") {
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
    public static function setValues($data, $eid = 0)
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
            if (! self::setValue($_key, $_value, $eid,$_describe)) {
                return false;
            }
        }
        return true;
    }

    /**
     * 设置配置项
     * @param mixed $key
     * @param mixed $value
     * @param number $eid
     * @param string $describe
     * @return boolean
     */
    public  static  function setValue($key,$value,$eid=0,$describe="",$type =2)
    {
        if (empty($key)) {
            return false;
        }
        if (! preg_match('#^[a-zA-Z_]{1}[a-zA-Z\d_]{3,50}$#', $key)) {
            ExLog::log("配置项[".$key."]不符合规范错误");
            return false;
        }
        $config = SystemConfig::get([
            "key" => $key,
            "eid" => $eid
        ]);
        $save_data = [
            'key' => $key,
            'value' => $value,
            'eid' =>$eid,
            'type' =>($config)?$config->getAttr("type"):$type,
            'status' => self::AVAILABLED_USED
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
        return $result;
    }
}