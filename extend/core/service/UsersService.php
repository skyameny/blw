<?php
/**
 * 用户管理服务
 */
namespace core\service;


use core\models\BlModel;
use core\models\User;
use core\exception\UserException;
use core\includes\user\UsersManagement;
use core\exception\CommonException;
use core\includes\session\SessionManagement;
use think\Db;
use core\utils\ExLog;
use core\models\Role;
use core\exception\PasswordException;
use core\includes\helper\HelperPassword;

/**
 * The UserService
 *
 * @access public
 * @author Dream
 * @core
 
 */
class UsersService
{
    
    CONST LEGACY_ALGORITHM = 'md5';
    CONST LEGACY_SALT_LENGTH = 0;
    
    /**
     *
     * @access private
     * @var 
     */
    private static $instance = null;
    
    
    /**
     * 返回helperpassword
     *
     * @return 
     */
    public static function getPasswordHash() {
        return new HelperPassword(
            defined('PASSWORD_HASH_ALGORITHM') ? PASSWORD_HASH_ALGORITHM : self::LEGACY_ALGORITHM,
            defined('PASSWORD_HASH_SALT_LENGTH') ? PASSWORD_HASH_SALT_LENGTH : self::LEGACY_SALT_LENGTH
            );
    }
    
    /**
     * 判断是否含有login
     */
    public function loginExists($login,  BlModel $class = null)
    {
        $returnValue = (bool) false;
        
        if(is_null($class)){
            $class = new User();
        }
        $users = $class->searchInstances(
            array("username" => $login)
            );
        
        if(count($users) > 0){
            $returnValue = true;
        }
        
        return (bool) $returnValue;
    }
    
    public function mobileExists($mobile,User $class = null)
    {
        $returnValue = (bool) false;
        
        if(is_null($class)){
            $class = new User();
        }
        $users = $class->searchInstances(
            array("mobile" => $mobile)
            );
        
        if(count($users) > 0){
            $returnValue = true;
        }
        
        return (bool) $returnValue;
    }

    /**
     * 添加用户
     *
     * {@inheritdoc}
     *
     * @see \core\includes\user\UsersManagement::addUser()
     */
    public function addUser($data, BlModel $role = null, BlModel $class = null)
    {
        $returnValue = null;
        $login = $data["username"];
        $mobile = $data["mobile"];
        
        if ($this->loginExists($login) || $this->mobileExists($mobile)) {
            throw new CommonException("Login '${login}' already in use.", STATUS_CODE_LOGIN_EXITS);
        } else {
            $role = (empty($role)) ? new Role(INSTANCE_ROLE_GENERIS) : $role;
            $userClass = (! empty($class)) ? $class : new User();
            $returnValue = $userClass->save(array(
                "username" => $login,
                "mobile" => $mobile,
                "nickname" => empty($data["nickname"]) ? $login : $data["nickname"],
                "passwd" => $this->userAdditionPasswordEncryption($login, $data["passwd"]),
                "create_time" => NOW_TIME,
                "gid" => $data["user_garden"],
                "avatar" => empty($data["user_avatar"]) ? DEFAULT_USER_AVATAR : $data["user_avatar"],
                "type" => 1,
                "status" => $data["status"]
            ));
            if (empty($returnValue)) {
                throw new CommonException("Unable to create user with login = '${login}'.",STATUS_CODE_USER_ADD_FAILED);
            }
        }
        
        return $returnValue;
    }

/**
 * 删除用户
 *
 * @access public
 * @author Dream
 * @param  Resource user A reference to the User to be removed from the persistent memory of Generis.
 * @return boolean
 */
public function removeUser( BlModel $user)
{
    $returnValue = (bool) false;
    $roles =$this->getUserRoles($user);
    if(!is_null($roles)){
        foreach ($roles as $role){
            $this->unnatachRole($user, $role);
        }
    }
    $returnValue = $user->delete();
    
    return (bool) $returnValue;
}


/**
 * Indicates if an Authenticated Session is open.
 *
 * @access public
 * @author 
 * @return boolean
 */
public function isASessionOpened()
{
    return !SessionManagement::isAnonymous();
}

/**
 * 
 */
public function isPasswordValid($password,  BlModel $user)
{
    $returnValue = (bool) false;
    
    if(!is_string($password)){
        throw new CommonException('The password must be of "string" type, got '.gettype($password),STATUS_CODE_PARAM_ERROR);
    }
    $returnValue = self::getPasswordHash()->verify($password, $hash);
    
    return (bool) $returnValue;
}

/**
 * 设置密码
 *
 * @access public
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @param  Resource user The user you want to set the password.
 * @param  string password The md5 hash of the password you want to set to the user.
 */
public function setPassword( BlModel $user, $password)
{
    if(!is_string($password)){
        throw new UserException('The password must be of "string" type, got '.gettype($password));
    }
    
    $user->editPropertyValues(new core_kernel_classes_Property(PROPERTY_USER_PASSWORD),core_kernel_users_Service::getPasswordHash()->encrypt($password));
}

/**
 * 获取用户的角色
 *
 * @access public
 * @author Dream 
 * @param  Resource user A Generis User.
 * @return array
 */
public function getUserRoles( BlModel $user)
{
    $roles = $user->getAttr('roles');
    return $roles;
}

/**
 * Indicates if a user is granted with a set of Roles.
 *
 * @access public
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @param  Resource user The User instance you want to check Roles.
 * @param  roles Can be either a single Resource or an array of Resource depicting Role(s).
 * @return boolean
 */
public function userHasRoles( BlModel $user, $roles)
{
    $returnValue = (bool) false;
    
    if (empty($roles)){
        throw new InvalidArgumentException('The $roles parameter must not be empty.');
    }
    
    $roles = (is_array($roles)) ? $roles : array($roles);
    $searchRoles = array();
    foreach ($roles as $r){
        $searchRoles[] = ($r instanceof BlModel) ? $r->getUri() : $r;
    }
    unset($roles);
    
    if (common_session_SessionManager::getSession()->getUserUri() == $user->getUri()){
        foreach (common_session_SessionManager::getSession()->getUserRoles() as $role) {
            if (in_array($role, $searchRoles)) {
                $returnValue = true;
                break;
            }
        }
    } else {
        // After introducing remote users, we can no longer guarantee that any user and his roles are available
        common_Logger::w('Roles of non current user ('.$user->getUri().') checked, trying fallback to local ontology');
        $userRoles = array_keys($this->getUserRoles($user));
        $identicalRoles = array_intersect($searchRoles, $userRoles);
        
        $returnValue = (count($identicalRoles) === count($searchRoles));
    }
    
    return (bool) $returnValue;
}

/**
 * Attach a Generis Role to a given Generis User. A UserException will be
 * if an error occurs. If the User already has the role, nothing happens.
 *
 * @access public
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @param  Resource user The User you want to attach a Role.
 * @param  Resource role A Role to attach to a User.
 * @return void
 */
public function attachRole( BlModel $user,  BlModel $role)
{
    try{
        if (false === $this->userHasRoles($user, $role)){
            $rolesProperty = new core_kernel_classes_Property(PROPERTY_USER_ROLES);
            $user->setPropertyValue($rolesProperty, $role);
        }
    }
    catch (common_Exception $e){
        $roleUri = $role->getUri;
        $userUri = $user->getUri();
        $msg = "An error occured while attaching role '${roleUri}' to user '${userUri}': " . $e->getMessage();
        throw new core_kernel_users_Exception($msg);
}
}

/**
 * Short description of method unnatachRole
 *
 * @access public
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @param  Resource user A Generis user from which you want to unnattach the Generis Role.
 * @param  Resource role The Generis Role you want to Unnatach from the Generis User.
 */
public function unnatachRole( BlModel $user,  BlModel $role)
{
    $flag = $user->roles()->detach($role->getAttr("id"));
    ExLog::log("解除用户与角色的关系：".Db::getLastSql(),ExLog::DEBUG);
    return boolval($flag);
}

/**
 * Add a role in Generis.
 *
 * @access public
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @param  string label The label to apply to the newly created Generis Role.
 * @param  includedRoles The Role(s) to be included in the newly created Generis Role. Can be either a Resource or an array of Resources.
 * @return core_kernel_classes_Resource
 */
public function addRole($label, $includedRoles = null, BlModel $class = null)
{
    $returnValue = null;
    
    $includedRoles = is_array($includedRoles) ? $includedRoles : array($includedRoles);
    $includedRoles = empty($includedRoles[0]) ? array() : $includedRoles;
    
    $classRole =  (empty($class)) ? new core_kernel_classes_Class(CLASS_ROLE) : $class;
    $includesRoleProperty = new core_kernel_classes_Property(PROPERTY_ROLE_INCLUDESROLE);
    $role = $classRole->createInstance($label, "${label} Role");
    
    foreach ($includedRoles as $ir){
        $role->setPropertyValue($includesRoleProperty, $ir);
    }
    
    $returnValue = $role;
    
    return $returnValue;
}

/**
 * 删除角色
 *
 * @access public
 * @author Dream
 * @param  Resource role The Role to remove.
 * @return boolean
 */
public function removeRole( BlModel $role)
{
    $returnValue = $role->remove();
    return (bool) $returnValue;
}

/**
 * Get an array of the Roles included by a Generis Role.
 *
 * 
 */
public function getIncludedRoles( BlModel $role)
{
    $returnValue = array();
    
    if (GENERIS_CACHE_USERS_ROLES === true && core_kernel_users_Cache::areIncludedRolesInCache($role) === true){
        $returnValue = core_kernel_users_Cache::retrieveIncludedRoles($role);
    }
    else{
        // We use a Depth First Search approach to flatten the Roles Graph.
        $includesRoleProperty = new core_kernel_classes_Property(PROPERTY_ROLE_INCLUDESROLE);
        $visitedRoles = array();
        $s = array(); // vertex stack.
        array_push($s, $role); // begin with $role as the first vertex.
        
        while (!empty($s)){
            $u = array_pop($s);
            
            if (false === in_array($u->getUri(), $visitedRoles, true)){
                $visitedRoles[] = $u->getUri();
                $returnValue[$u->getUri()] = $u;
                
                $ar = $u->getPropertyValuesCollection($includesRoleProperty);
                foreach ($ar->getIterator() as $w){
                    if (false === in_array($w->getUri(), $visitedRoles, true)){ // not visited
                        array_push($s, $w);
                    }
                }
            }
        }
        
        // remove the root vertex which is actually the role we are testing.
        unset($returnValue[$role->getUri()]);
        
        if (GENERIS_CACHE_USERS_ROLES === true){
            try{
                core_kernel_users_Cache::cacheIncludedRoles($role, $returnValue);
            }
            catch(core_kernel_users_CacheException $e){
                $roleUri = $role->getUri();
                $msg = "Unable to retrieve included roles from cache memory for role '${roleUri}': ";
                $msg.= $e->getMessage();
                throw new core_kernel_users_Exception($msg);
        }
    }
}

return (array) $returnValue;
}

/**
 * Returns an array of Roles (as Resources) where keys are their URIs. The
 * roles represent which kind of Roles are accepted to be identified against
 * system.
 *
 * @access public
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @return array
 */
public function getAllowedRoles()
{
    $returnValue = array();
    
    $role = new core_kernel_classes_Resource(INSTANCE_ROLE_GENERIS);
    $returnValue = array($role->getUri() => $role);
    
    return (array) $returnValue;
}

/**
 * Returns a Role (as a Resource) which represents the default role of the
 * If a user has to be created but no Role is given to him, it will receive
 * role.
 *
 * @access public
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @return core_kernel_classes_Resource
 */
public function getDefaultRole()
{
    $returnValue = null;
    
    $returnValue = new core_kernel_classes_Resource(INSTANCE_ROLE_GENERIS);
    
    return $returnValue;
}

/**
 * Make a Role include another Role.
 *
 * @access public
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @param  core_kernel_classes_Resource role The role that needs to include another role.
 * @param  core_kernel_classes_Resource Resource roleToInclude The role to be included.
 */
public function includeRole( BlModel $role,  array $roleToInclude)
{
    $includesRoleProperty = new core_kernel_classes_Property(PROPERTY_ROLE_INCLUDESROLE);
    
    // Clean to avoid double entries...
    $role->removePropertyValues($includesRoleProperty, array('like' => false, 'pattern' => $roleToInclude->getUri()));
    
    // Include the Role.
    $role->setPropertyValue($includesRoleProperty, $roleToInclude->getUri());
    
    // Reset cache.
    core_kernel_users_Cache::removeIncludedRoles($role);
}

/**
 * Uninclude a Role from antother Role.
 *
 * @access public
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @param core_kernel_classes_Resource role The Role from which you want to uninclude a Role.
 * @param core_kernel_classes_Resource roleToUninclude The Role to uninclude.
 */
public function unincludeRole(BlModel $role, $roleToUninclude)
{
    $includesRoleProperty = new core_kernel_classes_Property(PROPERTY_ROLE_INCLUDESROLE);
    $role->removePropertyValues($includesRoleProperty, array('like' => false, 'pattern' => $roleToUninclude->getUri()));
    
    // invalidate cache for the role.
    if (GENERIS_CACHE_USERS_ROLES == true){
        core_kernel_users_Cache::removeIncludedRoles($role);
        
        // For each roles that have $role for included role,
        // remove the cache entry.
        foreach ($this->getAllRoles() as $r){
            $includedRoles = $this->getIncludedRoles($r);
            
            if (array_key_exists($role->getUri(), $includedRoles)){
                core_kernel_users_Cache::removeIncludedRoles($r);
            }
        }
    }
    
}

/**
 * Log in a user into Generis that has one of the provided $allowedRoles.
 *
 * @access public
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @param  string login The login of the user.
 * @param  string password the md5 hash of the password.
 * @param  allowedRoles A Role or an array of Roles that are allowed to be logged in. If the user has a Role that matches one or more Roles in this array, the login request will be accepted.
 * @return boolean
 */
public function loginUser($login, $password, $allowedRoles=null)
{
    return LoginService::login($login, $password);
}

/**
 * The constructor is private to implement the Singleton Design Pattern.
 *
 * @access private
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 */
private function __construct()
{
    // Only to restrict instances of this class to a single instance.
}

/**
 * Get a unique instance of the UserService.
 *
 * @access public
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @return core_kernel_users_Service
 */
public static function singleton()
{
    $returnValue = null;
    
    if (!isset(self::$instance)) {
        $c = __CLASS__;
        self::$instance = new $c();
    }
    $returnValue = self::$instance;
    
    return $returnValue;
}

/**
 * Logout the current user. The session will be entirely reset.
 *
 * @access public
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @return boolean
 */
public function logout()
{
    return \common_session_SessionManager::endSession();
}

/**
 * Returns the whole collection of Roles in Generis.
 *
 * @return array An associative array where keys are Role URIs and values are instances of the core_kernel_classes_Resource PHP class.
 */
public function getAllRoles()
{
    $roleClass = new core_kernel_classes_Class(CLASS_ROLE);
    return $roleClass->getInstances(true);
}

/**
 * Trigger user encrypition at user insertion time.
 *
 * @param string $login
 * @param string $password
 *
 * @return string The encrypted password.
 */
protected function userAdditionPasswordEncryption($login, $password)
{
    try {
        return static::getPasswordHash()->encrypt($password);
    } catch (PasswordException $e) {
        throw new UserException($e->getMessage(),PASSWD_TYPE_ERROR);
        return false;
    }
    
}
}
