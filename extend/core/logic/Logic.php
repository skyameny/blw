<?php
/**
 * 运行逻辑层
 * User: keepwin100
 * Date: 2019-06-02
 * Time: 23:53
 */

namespace core\logic;

use core\utils\ExLog;

class Logic
{
    protected static $instances = [];

    public static function singleton() {
        $returnValue = null;

        $serviceName = get_called_class();
        if (!isset(self::$instances[$serviceName])) {
            self::$instances[$serviceName] = new $serviceName();
        }
        $returnValue = self::$instances[$serviceName];

        return $returnValue;
    }

    protected function __construct()
    {
        ;
    }
    /**
     * 逻辑层不允许复制
     * @return bool
     */
    public function __clone()
    {
        return false;
    }

    public function log($message,$level)
    {
        ExLog::log($message,$level);
    }


}