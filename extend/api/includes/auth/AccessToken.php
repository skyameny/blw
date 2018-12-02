<?php
namespace api\includes\auth;

use core\includes\helper\HelperRandom;
use core\utils\ExLog;

class AccessToken
{
    protected $algo;
    protected $enterprise_uid;
    protected $appid;
    private $token_string = "";
    
    public function __construct($ep_uid,$appid,$algo="md5")
    {
        $this->enterprise_uid = $ep_uid;
        $this->appid = $appid;
        $this->algo = $algo;
    }
    
    /**
     * 创建token
     * appid+16为随机数+eid  *唯一
     * 
     * @return string
     */
    public function create()
    {
        $randstring = HelperRandom::generateString(16);
        $randstring = $this->appid.$randstring.$this->enterprise_uid;
        ExLog::log("token加密前：".$randstring,ExLog::DEBUG);
        $hash = hash($this->algo, $randstring);
        return $hash;
    } 
}