<?php
namespace core\includes\user;

class ApiUser extends GenerisUser
{
    public function getToken()
    {
        $resource = $this->getUserResource();
        
    }
    
}
