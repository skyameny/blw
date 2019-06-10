<?php
/**
 * ACtion缓存
 * User: keepwin100
 * Date: 2019-05-10
 * Time: 14:19
 */
namespace authority\utils;

use core\model\Role;
use think\Cache;

class CacheActions
{
    protected static $namespace = "cache_action_";

    protected static function getCacheName(Role $role)
    {
        return self::$namespace.$role->getAttr("id");
    }

    public static function cache(Role $role)
    {
        $actions = $role->with(['authRule.Actions']);
        if(!is_array($actions)|| empty($actions))
        {
            return false;
        }
        return Cache::set(self::getCacheName($role),$actions);
    }

    /**
     * 更新
     */
    public static function refresh(Role $role)
    {
        return self::cache($role);
    }

    public static function has($role){
        return Cache::has(self::getCacheName($role));
    }

    public static function getCache($role)
    {
        return Cache::get(self::getCacheName($role));
    }
}