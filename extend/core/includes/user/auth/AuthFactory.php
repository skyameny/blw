<?php
namespace core\includes\user\auth;

class AuthFactory 
{
    /**
     *
     * @param unknown $type            
     */
    public static function creatadapter($extend = null)
    {
        $adpater = null;
        if (!is_null($extend)) {
            $clazz = $extend . "\\includes\\user\\auth\\AuthAdapter";
            if (class_exists($clazz)) {
                $adpater = new $clazz();
            }
        }
        if (empty($adpater)) {
            $adpater = new AuthAdapter();
        }
        return $adpater;
    }
}