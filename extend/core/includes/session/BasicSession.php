<?php
namespace core\includes\session;

use core\includes\user\User;
use core\models\SystemConfig;


class BasicSession implements Session
{
    /**
     * @var User
     */
    private $user;

    
    public function __construct(User $user) {
        $this->user = $user;
    }
    
    public function getUser() {
        return $this->user;
    }
    
    public function getUserId() {
        return $this->user->getIdentifier();
    }
    
    /**
     * @param string $property
     * @return mixed
     */
    public function getUserPropertyValues($property) {
        return $this->user->getUserResource()->getAttr($property);
    }
    
    /**
     * (non-PHPdoc)
     * @see Session::getUserLabel()
     */
    public function getUserLabel() {
        $label = '';
        $label = $this->user->getPropertyValues("nickname");
        return $label;
    }
    
    /**
     * (non-PHPdoc)
     * @see ::getUserRoles()
     */
    public function getUserRoles() {
        $returnValue = array();
        // We use a Depth First Search approach to flatten the Roles Graph.
        foreach ($this->user->getRoles() as $role){
            //foreach (UsersService::singleton()->getIncludedRoles($role) as $incRole) {
            $returnValue[] = $role->visible(["name","id"])->toArray();
            //}
        }
        return $returnValue;
    }
    
    /**
     * (non-PHPdoc)
     * @see 
     */
    public function getDataLanguage() {
        $lang = $this->user->getPropertyValues("lang");
        return empty($lang) ? DEFAULT_LANG : (string)current($lang);
    }
    
    /**
     * (non-PHPdoc)
     * @see Session::getInterfaceLanguage()
     */
    public function getInterfaceLanguage() {
        $lang = $this->user->getPropertyValues("lang");
        return empty($lang) ? DEFAULT_LANG : (string)current($lang);
    }
    
    /**
     * (non-PHPdoc)
     * @see common_session_Session::getTimeZone()
     */
    public function getTimeZone() {
        $tzs = SystemConfig::getValue(PROPERTY_USER_TIMEZONE);
        $tz = empty($tzs) ? '' : (string)current($tzs);
        return empty($tz) ? TIME_ZONE : $tz;
    }
    
    public function refresh() {
        if( method_exists($this->user, "refresh") ){
            $this->user->refresh();
        }
    }
    
    public function getGarden(){
        
    }
}