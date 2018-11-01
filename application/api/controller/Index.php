<?php
namespace app\api\controller;

use core\controller\Api;
use core\controller\Base;
use think\Controller;
use core\controller\tool\EncryptionAttribute;

class Index extends Api
{
   use EncryptionAttribute;
   
    public function index()
    {
        
        $request = $this->apiParams();
        
        $res = $this->decode_AES_ECB($request,"sssss");
        
        
        
        $asy = ["name"=>"${res}","age"=>34];
        $this->result($asy);
    }
}
