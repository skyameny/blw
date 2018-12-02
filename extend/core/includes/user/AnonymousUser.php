<?php
namespace core\includes\user;

class AnonymousUser implements User
{
    /**
     * (non-PHPdoc)
     * @see \oat\oatbox\user\User::getIdentifier()
     */
    public function getIdentifier() {
        return null;
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\oatbox\user\User::getRoles()
     */
    public function getRoles() {
        return array(INSTANCE_ROLE_ANONYMOUS);
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\oatbox\user\User::getPropertyValues()
     */
    public function getPropertyValues($property) {
        return array();
    }
}