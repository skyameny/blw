<?php
/**
 * auth适配器
 */
namespace core\includes\user\auth;

use core\service\UsersService;
use core\exception\AuthFailedException;
use core\includes\user\GenerisUser;
use core\model\User as UserModel;


class AuthAdapter implements LoginAdapter
{
    public static function getPasswordHash() {
        return UsersService::getPasswordHash();
    }
    
    /**
     * Username to verify
     * 
     * @var string
     */
    private $username;
    
    /**
     * Password to verify
     * 
     * @var $password
     */
	private $password;
	
	/**
	 * 
	 * @param array $configuration
	 */
	public function setOptions(array $options) {
	    // nothing to configure
	}
	
	/**
	 * (non-PHPdoc)
	 * @see 
	 */
	public function setCredentials($login, $password) {
	    $this->username = $login;
	    $this->password = $password;
	}
	
	/**
     * (non-PHPdoc)
     * @see authAdapter::authenticate()
     */
    public function authenticate() {
    	
        $userModel = new UserModel();
    	$filters = array('username|mobile','like',$this->username);
    	$users = $userModel->where('username|mobile','like',$this->username)->where(["status"=>1])->select();
    	
    	if (count($users) > 1){
    		// Multiple users matching
    	    throw new AuthFailedException("Multiple Users found with the same login '".$this->username."'.");
    	}
        if (empty($users)){
            if (!UsersService::getPasswordHash()->verify($this->password, "")) {
                throw new AuthFailedException('Unknown user "'.$this->username.'"');
            }
            // should never happen, added for integrity
            throw new AuthFailedException('Inexisting user did not fail password check, this should not happen');
    	}
    	
	    $userResource = current($users);
	    $hash = $userResource->getAttr("passwd");
	    if (!UsersService::getPasswordHash()->verify($this->password, $hash)) {
	        throw new AuthFailedException("Invalid password for user '".$this->username.'"');
	    }
	    
    	return new GenerisUser($userResource);
    }
}