<?php
namespace core\includes\helper;
/**
 * 随机算法
 * 
 * @author Dream
 *
 */
class HelperRandom {
    
    /**
     * 生成随机字符串
     */
    static public function generateString($length) {
        $token = '';
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $maxIndex = strlen($chars) - 1;

        for ($i = 0; $i < $length; $i++) {
            $token .= $chars[mt_rand(0, $maxIndex)];
        }
         
        return $token;
    }

    /**
     * 生成随机数字 （包括0开头）
     */
    static public function generateNumber($length) {
        $token = '';
        $chars = '0123456789';
        $maxIndex = strlen($chars) - 1;

        for ($i = 0; $i < $length; $i++) {
            $token .= $chars[mt_rand(0, $maxIndex)];
        }

        return $token;
    }
}