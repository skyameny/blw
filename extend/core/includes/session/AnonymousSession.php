<?php
namespace core\includes\session;


use core\includes\user\AnonymousUser;

class AnonymousSession implements StatelessSession
{
    /**
     * (non-PHPdoc)
     * @see common_session_Session::getUser()
     */
    public function getUser() {
        return new AnonymousUser();
    }
    
    public function getGarden(){
        return null;
    }
    
    public function getUserId() {
        return null;
    }

    /**
     * (non-PHPdoc)
     * @see common_session_Session::getDataLanguage()
     */
    public function getUserLabel() {
        return "游客";
    }    
    
    /**
     * (non-PHPdoc)
     * @see common_session_Session::getUserRoles()
     */
    public function getUserRoles() {
        return array(INSTANCE_ROLE_ANONYMOUS);
    }
    
    /**
     * (non-PHPdoc)
     * @see common_session_Session::getDataLanguage()
     */
    public function getDataLanguage() {
        return DEFAULT_LANG;
    }
    
    /**
     * (non-PHPdoc)
     * @see common_session_Session::getInterfaceLanguage()
     */
    public function getInterfaceLanguage() {
        return defined('DEFAULT_ANONYMOUS_INTERFACE_LANG') ? DEFAULT_ANONYMOUS_INTERFACE_LANG : DEFAULT_LANG;
    }
    
    /**
     * (non-PHPdoc)
     * @see common_session_Session::getTimeZone()
     */
    public function getTimeZone() {
        return TIME_ZONE;
    }
    
    /**
     * (non-PHPdoc)
     * @see common_session_Session::getUserPropertyValues()
     */
    public function getUserPropertyValues($property) {
       return array(); 
    }
    
    /**
     * (non-PHPdoc)
     * @see common_session_Session::refresh()
     */
    public function refresh() {
        // nothing to do here
    }

}