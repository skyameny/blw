<?php

class ExtensionLoader
{
    /**
     * 加载扩展
     * @param array $extraConstants
     */
    public static function load($extraConstants = array())
    {
        if(empty($extraConstants)){
            return false;
        }
        foreach ($extraConstants as $key => $extension){
            if(!defined($key) && !is_array($extension)){
                define($key, $extension);
            }
            $extension_dir = EXTEND_PATH.$extension;
            $constantFile = $extension_dir.DS."common".DS."const.php";
            if (is_file($constantFile)) {
                //include the constant file
                include_once $constantFile;
            }
        }
    }
        
        
    
        
}