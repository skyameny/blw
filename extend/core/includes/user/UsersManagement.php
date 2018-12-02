<?php
namespace core\includes\user;

use core\model\BlModel;

interface UsersManagement
{
    

    public function loginExists($login,  BlModel $class = null);
    
    /**
     * 
     * @param unknown $login
     * @param unknown $password
     * @param BlModel $role
     */
    public function addUser($user_data,  BlModel $role = null);
    
    /**
     * 
     * @param BlModel $user
     */
    public function removeUser( BlModel $user);
    
    /**
     * 
     * @param unknown $login
     * @param BlModel $class
     */
    public function getOneUser($login,  BlModel $class = null);
    
    /**
     * 
     */
    public function isASessionOpened();
    
    /**
     * 
     * @param unknown $password
     * @param BlModel $user
     */
    public function isPasswordValid($password,  BlModel $user);
    
    /**
     * 
     * @param BlModel $user
     * @param unknown $password
     */
    public function setPassword( BlModel $user, $password);
    
    /**
     * 
     * @param BlModel $user
     */
    public function getUserRoles( BlModel $user);
    
    /**
     * 
     * @param BlModel $user
     * @param unknown $roles
     */
    public function userHasRoles( BlModel $user, $roles);
    
    /**
     * 
     * @param BlModel $user
     * @param BlModel $role
     */
    public function attachRole( BlModel $user,  BlModel $role);
    
    /**
     * 
     * @param BlModel $user
     * @param BlModel $role
     */
    public function unnatachRole( BlModel $user,  BlModel $role);
    
    /**
     * 
     */
    public function getAllowedRoles();
    
    /**
     * 
     */
    public function getDefaultRole();
    
} /* end of interface core_kernel_users_UsersManagement */

?>