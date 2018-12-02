<?php 
namespace core\includes\helper;

use think\Request;

class HelperSystem
{
    /**
     * 获取root地址
     *
     * @return mixed
     */
    public static function getRoot()
    {
        $request = Request::instance();
        $root    = $request->domain();
        $root    = str_replace('/index.php', '', $root);
        if (defined('APP_NAMESPACE') && APP_NAMESPACE == 'api') {
            $root = preg_replace('/\/api$/', '', $root);
            $root = rtrim($root, '/');
        }
        return $root;
    }
    
    public static  function getOption($key)
    {
        if (!is_string($key) || empty($key)) {
            return [];
        }
        $optionValue = "";
        
        return $optionValue;
    }
}