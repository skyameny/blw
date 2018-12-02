<?php

namespace core\includes\user;


/**
 * Abstract User
 *
 * @access public
 * @author Dream
 * @package generis
 */
interface User
{
    
    const ADMIN_TYPE_SUPER = 0;
    
    const ADMIN_TYPE_GARDAN = 1;
    
    /**
     * Returns the unique identifier of the user
     *
     * @return string
     */
    public function getIdentifier();
    
    /**
     * Extends the users explizit roles with the implizit rules
     * of the local system
     *
     * @return array the identifiers of the roles:
     */
    public function getRoles();
    
    /**
     * Retrieve custom attributes of a user
     *
     * @param string $attribute
     * @return array an array of strings
     */
    public function getPropertyValues($property);
}


?>