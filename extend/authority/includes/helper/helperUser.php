<?php 
namespace core\includes\helper;

use think\Config;
use think\Request;

class helperUser
{

    /**
     * 获取当前用户的主题
     * @return unknown|unknown|mixed|NULL|void|boolean|unknown[]
     */
    public static function getCurrentTheme()
    {
        static $_currentTheme;
        
        if (! empty($_currentTheme)) {
            return $_currentTheme;
        }
        
        $t = 'user_theme';
        $theme = Config::get("user_default_theme");
        
        $cmfDetectTheme = true;
        if ($cmfDetectTheme) {
            if (isset($_GET[$t])) {
                $theme = $_GET[$t];
                cookie('bl_user_theme', $theme, 864000);
            } elseif (cookie('bl_user_theme')) {
                $theme = cookie('bl_user_theme');
            }
        }
        
        $_currentTheme = $theme;
        
        return $theme;
    }
    
    /**
     * 管理员主题
     * @return unknown|unknown|mixed|NULL|void|boolean|unknown[]
     */
    public static function getAdminTheme()
    {
        static $_currentTheme;
        
        if (! empty($_currentTheme)) {
            return $_currentTheme;
        }
        
        $t = 'admin_theme';
        $theme = Config::get("user_admin_theme");
        
        $cmfDetectTheme = true;
        if ($cmfDetectTheme) {
            if (isset($_GET[$t])) {
                $theme = $_GET[$t];
                cookie('bl_admin_theme', $theme, 864000);
            } elseif (cookie('bl_admin_theme')) {
                $theme = cookie('bl_admin_theme');
            }
        }
        
        $_currentTheme = $theme;
        
        return $theme;
    }
    
    
    /**
     * 获取root地址
     * 
     * @return mixed
     */
    public static  function getRoot()
    {
        $request = Request::instance();
        $root    = $request->root();
        $root    = str_replace('/index.php', '', $root);
        if (defined('APP_NAMESPACE') && APP_NAMESPACE == 'api') {
            $root = preg_replace('/\/api$/', '', $root);
            $root = rtrim($root, '/');
        }
        
        return $root;
    }
    
    
}


?>