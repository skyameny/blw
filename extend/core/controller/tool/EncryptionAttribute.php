<?php
/**
 * encrytionAttrbute
 * 
 */
namespace  core\controller\tool;

use think\Request;

trait EncryptionAttribute
{
    
    protected  $secret_key = "ca8416364fa1d14b59b0c8c3b8b1c92a";
    
    public  function encode_AES_ECB($data,$secret_key){
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128,'',MCRYPT_MODE_ECB,'');
        
        $blocksize = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128,MCRYPT_MODE_ECB);
        $pad = $blocksize - (strlen($data) % $blocksize);
        $data = $data . str_repeat(chr($pad), $pad);
        
        $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td,$secret_key,$iv);
        $encrypted = mcrypt_generic($td,$data);
        mcrypt_generic_deinit($td);
        return $encrypted;
    }
    public  function decode_AES_ECB($data,$secret_key){
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128,'',MCRYPT_MODE_ECB,'');
        $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td,$secret_key,$iv);
        $data = mdecrypt_generic($td,$data);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        
        $dec_s = strlen($data);
        $padding = ord($data[$dec_s-1]);
        $data = substr($data, 0, -$padding);
        
        return trim($data);
    }
    
    /**
     * api获取值
     * @return string
     */
    public function apiParams()
    {
        $request = Request::instance();
        $http_param_query =http_build_query($request->param());
        $data = $this->encode_AES_ECB($http_param_query, $this->secret_key);
        return $data;
    }
    /**
     * api输出值
     */
    public function apiResult($data)
    {
        $http_query = http_build_query($data);
        
    }
}