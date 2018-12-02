<?php
/**
 * 单用户管理
 */
namespace core\service;

use core\includes\user\UsersManagement;
use core\exception\UserException;
use core\utils\ExLog;
use core\model\BlModel;

use core\model\User;
use core\includes\session\SessionManagement;
use core\exception\AuthFailedException;

/**
 * This class provide service on user management
 *
 * @access public
 * @author 
 * @package tao
 */
class UserService extends Service implements UsersManagement
{

    /**
     * the core user service
     *
     * @access protected
     * @var 
     */
    protected $generisUserService = null;

    /**
     * constructor
     *
     * @access protected
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return mixed
     */
    protected function __construct()
    {
		$this->generisUserService = UsersService::singleton();
    }

    /**
     * 
     * @param unknown $login
     * @param unknown $password
     * @return boolean
     */
    public function loginUser($login, $password)
    {
        
        $returnValue = (bool) false;
        try{
            $returnValue = LoginService::login($login, $password);
        }
        catch(AuthFailedException $ue){
        	ExLog::log("A fatal error occured at user login time: " . $ue->getMessage());
        }
        return (bool) $returnValue;
    }

    /**
     * retrieve the logged in user
     *
     * @access public
     * @author 
     * @return BlModel
     */
    public function getCurrentUser()
    {
        $returnValue = null;

    	if(SessionManagement::isAnonymous()){
    	    $userid = SessionManagement::getSession()->getUser()->getIdentifier();
        	if(!empty($userid)){
        	    $returnValue = User::get($userid);
			} else {
				ExLog::log('no user');
			}
    	}

        return $returnValue;
    }

    /**
     * Check if the login is already used
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param string login
     * @param 
     * @return boolean
     */
    public function loginExists($login, BlModel $class = null)
    {
        $returnValue = (bool) false;

        $returnValue = $this->generisUserService->loginExists($login, $class);

        return (bool) $returnValue;
    }

    /**
     * Check if the login is available (because it's unique)
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string login
     * @return boolean
     */
    public function loginAvailable($login)
    {
        $returnValue = (bool) false;

		if(!empty($login)){
			$returnValue = !$this->loginExists($login);
		}

        return (bool) $returnValue;
    }

    /**
     * Get a user that has a given login.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param string login the user login is the unique identifier to retrieve him.
     * @param core_kernel_classes_Class A specific class to search the user.
     * @return core_kernel_classes_Resource
     */
    public function getOneUser($login, BlModel $class = null)
    {
        $returnValue = null;

		if (empty($login)){
			throw new common_exception_InvalidArgumentType('Missing login for '.__FUNCTION__);
		}
			
		$class = (!empty($class)) ? $class : $this->getRootClass();
		
		$user = $this->generisUserService->getOneUser($login, $class);
		
		if (!empty($user)){
			
			$userRolesProperty = new core_kernel_classes_Property(PROPERTY_USER_ROLES);
			$userRoles = $user->getPropertyValuesCollection($userRolesProperty);
			$allowedRoles = $this->getAllowedRoles();
			
			if($this->generisUserService->userHasRoles($user, $allowedRoles)){
				$returnValue = $user;
			} else {
			    common_Logger::i('User found for login \''.$login.'\' but does not have matchign roles');
			}
		} else {
			common_Logger::i('No user found for login \''.$login.'\'');
		}

        return $returnValue;
    }

    /**
     * Get the list of users by role(s)
     * options are: order, orderDir, start, end, search
     * with search consisting of: field, op, string
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array roles
     * @param  array options the user list options to order the list and paginate the results
     * @return array
     */
    public function getUsersByRoles($roles, $options = array())
    {
        $returnValue = array();

        //the users we want are instances of the role
		$fields = array('login' => PROPERTY_USER_LOGIN,
						'password' => PROPERTY_USER_PASSWORD,
						'uilg' => PROPERTY_USER_UILG,
						'deflg' => PROPERTY_USER_DEFLG,
						'mail' => PROPERTY_USER_MAIL,
		    			'email' => PROPERTY_USER_MAIL,
						'role' => RDF_TYPE,
						'roles' => PROPERTY_USER_ROLES,
						'firstname' => PROPERTY_USER_FIRSTNAME,
						'lastname' => PROPERTY_USER_LASTNAME,
						'name' => PROPERTY_USER_FIRSTNAME);
		
		$ops = array('eq' => "%s",
					 'bw' => "%s*",
					 'ew' => "*%s",
					 'cn' => "*%s*");
		
		$opts = array('recursive' => true, 'like' => false);
		if (isset($options['start'])) {
			$opts['offset'] = $options['start'];
		}
		if (isset($options['limit'])) {
			$opts['limit'] = $options['limit'];
		}
		
		$crits = array(PROPERTY_USER_LOGIN => '*');
		if (isset($options['search']) && !is_null($options['search']) && isset($options['search']['string']) && isset($ops[$options['search']['op']])) {
			$crits[$fields[$options['search']['field']]] = sprintf($ops[$options['search']['op']], $options['search']['string']);
		}
		// restrict roles
		$crits[PROPERTY_USER_ROLES] = $roles;
		
		if (isset($options['order'])) {
			$opts['order'] = $fields[$options['order']]; 
			if (isset($options['orderDir'])) {
				$opts['orderdir'] = $options['orderDir']; 
			}
		}
		
		$userClass = new core_kernel_classes_Class(CLASS_GENERIS_USER);
		
		$returnValue = $userClass->searchInstances($crits, $opts);

        return (array) $returnValue;
    }

    /**
     * Remove a user
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  core_kernel_classes_Resource $user
     * @return boolean
     */
    public function removeUser( BlModel $user)
    {
        $returnValue = (bool) false;

        if(!is_null($user)){
			$returnValue = $this->generisUserService->removeUser($user);
            $this->getEventManager()->trigger(new UserRemovedEvent($user->getUri()));
		}

        return (bool) $returnValue;
    }

    /**
     * returns a list of all concrete roles(instances of CLASS_ROLE)
     * which are allowed to login
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return array
     */
    public function getAllowedRoles()
    {
        $returnValue = array();

        $returnValue = array(INSTANCE_ROLE_BACKOFFICE => new core_kernel_classes_Resource(INSTANCE_ROLE_BACKOFFICE));

        return (array) $returnValue;
    }
    
    public function getDefaultRole()
    {
    	return new core_kernel_classes_Resource(INSTANCE_ROLE_BACKOFFICE);
    }

    /**
     * Short description of method logout
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return boolean
     */
    public function logout()
    {
        $returnValue = (bool) false;

        $returnValue = $this->generisUserService->logout();

        return (bool) $returnValue;
    }

    /**
     * Short description of method getAllUsers
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param array $options
	 * @param array $filters
     * @return array
     */
    public function getAllUsers($options = [], $filters = [PROPERTY_USER_LOGIN => '*'])
    {
        $userClass = new core_kernel_classes_Class(CLASS_TAO_USER);
		$options = array_merge(['recursive' => true, 'like' => true], $options);

		return (array) $userClass->searchInstances($filters, $options);
    }

	/**
	 * Returns count of instances, that match conditions in options and filters
	 * @access public
	 * @author Ivan Klimchuk <klimchuk@1pt.com>
	 * @param array $options
	 * @param array $filters
	 * @return int
     */
	public function getCountUsers($options = [], $filters = [])
	{
		$userClass = new core_kernel_classes_Class(CLASS_TAO_USER);

		return $userClass->countInstances($filters, $options);
	}

    /**
     * returns the nr of users full filling the criteria,
     * uses the same syntax as getUsersByRole
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array roles
     * @param  array options
     * @return int
     */
    public function getUserCount($roles, $options = array())
    {
        $returnValue = (int) 0;

        $opts = array(
        	'recursive' => true,
        	'like' => false
        );

		$crits = array(PROPERTY_USER_LOGIN => '*');
		if (isset($options['search']['string']) && isset($options['search']['op'])
			&& !empty($options['search']['string']) && !empty($options['search']['op'])) {
			$fields = array('login' => PROPERTY_USER_LOGIN,
						'password' => PROPERTY_USER_PASSWORD,
						'uilg' => PROPERTY_USER_UILG,
						'deflg' => PROPERTY_USER_DEFLG,
						'mail' => PROPERTY_USER_MAIL,
		    			'email' => PROPERTY_USER_MAIL,
						'role' => RDF_TYPE,
						'roles' => PROPERTY_USER_ROLES,
						'firstname' => PROPERTY_USER_FIRSTNAME,
						'lastname' => PROPERTY_USER_LASTNAME,
						'name' => PROPERTY_USER_FIRSTNAME);
			$ops = array('eq' => "%s",
					 'bw' => "%s*",
					 'ew' => "*%s",
					 'cn' => "*%s*");
			$crits[$fields[$options['search']['field']]] = sprintf($ops[$options['search']['op']], $options['search']['string']);
		}
		
		$crits[PROPERTY_USER_ROLES] = $roles;
		
		$userClass = new core_kernel_classes_Class(CLASS_GENERIS_USER);
		$returnValue = $userClass->countInstances($crits, $opts);

        return (int) $returnValue;
    }

    /**
     * Short description of method toTree
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  core_kernel_classes_Class $clazz
     * @param  array $options
     * @return array
     */
    public function toTree( BlModel $clazz, array $options = array())
    {
        $returnValue = array();

    	$users = $this->getAllUsers(array('order' => PROPERTY_USER_LOGIN));
		foreach($users as $user){
			$login = (string) $user->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_USER_LOGIN));
			$returnValue[] = array(
					'data' 	=> tao_helpers_Display::textCutter($login, 16),
					'attributes' => array(
						'id' => tao_helpers_Uri::encode($user->getUri()),
						'class' => 'node-instance'
					)
				);

		}

        return (array) $returnValue;
    }
    
    /**
     * Add a new user.
     */
    public function addUser($data, BlModel $role = null, BlModel $class = null){
		
    	if (empty($class)){
    		$class = $this->getRootClass();
    	}
    	
        $user = $this->generisUserService->addUser($data, $role, $class);
        
        //set up default properties
        if(!is_null($user)){
            $user->setPropertyValue("creat_time", time());
        }
    	
        return $user;
    }
	
	/**
	 * Indicates if a user session is currently opened or not.
	 * 
	 * @return boolean True if a session is opened, false otherwise.
	 */
	public function isASessionOpened() {
	    return AuthService::singleton()->isASessionOpened();
	}
	
	/**
	 * Indicates if a given user has a given password.
	 * 
	 * @param string password The password to check.
	 * @param core_kernel_classes_Resource user The user you want to check the password.
	 * @return boolean
	 */
	public function isPasswordValid($password,  BlModel $user){
		return $this->generisUserService->isPasswordValid($password, $user);
	}
	
	/**
	 * Change the password of a given user.
	 * 
	 * @param core_kernel_classes_Resource user The user you want to change the password.
	 * @param string password The md5 hash of the new password.
	 */
	public function setPassword(BlModel $user, $password){
		return $this->generisUserService->setPassword($user, $password);
	}
	
	/**
	 * Get the roles of a given user.
	 * 
	 * @param core_kernel_classes_Resource $user The user you want to retrieve the roles.
	 * @return array An array of core_kernel_classes_Resource.
	 */
	public function getUserRoles(BlModel $user){
		return $this->generisUserService->getUserRoles($user);
	}
	
	/**
	 * Indicates if a user is granted with a set of Roles.
	 *
	 * @access public
	 * @author Jerome Bogaerts, <jerome@taotesting.com>
	 * @param  Resource user The User instance you want to check Roles.
	 * @param  roles Can be either a single Resource or an array of Resource that are instances of Role.
	 * @return boolean
	 */
	public function userHasRoles(BlModel $user, $roles){
		return $this->generisUserService->userHasRoles($user, $roles);
	}
	
	/**
	 * Attach a Generis Role to a given TAO User. A UserException will be
	 * if an error occurs. If the User already has the role, nothing happens.
	 *
	 * @access public
	 * @author Jerome Bogaerts, <jerome@taotesting.com>
	 * @param  Resource user The User you want to attach a Role.
	 * @param  Resource role A Role to attach to a User.
	 * @throws core_kernel_users_Exception If an error occurs.
	 */
	public function attachRole(BlModel $user, BlModel $role)
	{
		$this->generisUserService->attachRole($user, $role);
	}
	
	/**
	 * Unnatach a Role from a given TAO User.
	 *
	 * @access public
	 * @author Jerome Bogaerts, <jerome@taotesting.com>
	 * @param  Resource user A TAO user from which you want to unnattach the Role.
	 * @param  Resource role The Role you want to Unnatach from the TAO User.
	 * @throws core_kernel_users_Exception If an error occurs.
	 */
	public function unnatachRole(BlModel $user, BlModel $role)
	{
		$this->generisUserService->unnatachRole($user, $role);
	}
        
	/**
	 * Get the class to use to instantiate users.
	 * 
	 * @return core_kernel_classes_Class The user class.
	 */
	public function getRootClass()
	{
		return new core_kernel_classes_Class(CLASS_TAO_USER);
	}

    /**
     * @param core_kernel_classes_Class $clazz
     * @param string $label
     * @return core_kernel_classes_Resource
     */
	public function createInstance(BlModel $clazz, $label = '')
    {
        $user = parent::createInstance($clazz, $label); // TODO: Change the autogenerated stub
        $this->getEventManager()->trigger(new UserCreatedEvent($user));
        return $user;
    }
}
