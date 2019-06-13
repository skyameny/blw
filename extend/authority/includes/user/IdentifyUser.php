<?php

namespace authority\includes\user;


/**
 * Abstract User
 *
 * @access public
 * @author Dream
 * @package generis
 */
interface IdentifyUser
{

    const ADMIN_TYPE_SUPER = 0;

    const ADMIN_TYPE_GARDEN = 1;

    /**
     * Returns the unique identifier of the user
     *
     * @return string
     */
    public function getIdentifier();

    public function getUserResource();

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

    public function refresh();
}


?>