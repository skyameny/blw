<?php
namespace  core\includes\token;

use core\includes\helper\HelperRandom;

class Token
{
    protected $token_string ="";
    
    public function build(){
        $this->token_string = HelperRandom::generateString(32);
    }
    
    public function verification($user,$token){
        
    }
    
    public function getToken(){
        
    
    }
    
    
    
}

