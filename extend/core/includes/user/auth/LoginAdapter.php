<?php
namespace core\includes\user\auth;

interface LoginAdapter 
{
    /**
     * Create an Adapter from a configuration
     *
     * @param array $configuration
     */
    public function setOptions(array $options);
    
    /**
     * Adapter must be able to store the login and password of the potential user
     *
     * @param string $login
     * @param string $password
     */
    public function setCredentials($login, $password);
    
    public function authenticate();
}