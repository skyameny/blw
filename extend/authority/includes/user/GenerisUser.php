<?php
/**
 * 用户权限
 * 
 * 管理员
 * session用户
 */
namespace authority\includes\user;

use authority\model\User as UserModel;
use think\Config;

class GenerisUser implements IdentifyUser
{

    protected $roles = array();
    
    private $identifier= 0;
    
    private $userResource;
    
    private $gardenResource; //社区

    private $cache;

    private $cachedProperties = array(
            "nickname",
            "username",
            "gid",
            "mobile",
            "layout",//外观
    );

    public function __construct(UserModel $user)
    {
        $this->identifier = $user->getAttr("id");
        foreach ($this->cachedProperties as $property){
            $this->getPropertyValues($property);
        }
        
        $this->getPropertyValues("gid");
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @return UserModel|null
     * @throws \think\exception\DbException
     */
    public function getUserResource()
    {
        return UserModel::get($this->getIdentifier());
    }
    
    /**
     * 获取社区信息
     */
    private function getGardenResource()
    {
        return Garden::get($this->getPropertyValues("gid"));
    }
    

    public function getPropertyValues($property)
    {
        if (!in_array($property, $this->cachedProperties)) {
            return $this->getUncached($property);
        } elseif (!isset($this->cache[$property])) {
            $this->cache[$property] = $this->getUncached($property);
        }

        return $this->cache[$property];

    }
    
    private function getUncached($property)
    {
        $value = array();
        switch ($property) {
            case "gid":
                return $this->getUserResource()->getGardenId();
                break;
            case "layout":
                return Config::get("default_layout");
	    	    break;
	    	default:
	    	    return $this->getUserResource()->getAttr($property);
	    }
	}
	
	public function refresh() {
	    $this->roles = false;
	    $this->cache = array(
	        "gdid" => $this->getUncached("gdid")
	    );
	    return true;
	}	
	
	/**
	 * 获取用户角色
	 * {@inheritDoc}
	 * @see \core\includes\user\User::getRoles()
	 */
	public function getRoles()
	{
	    $returnValue = array();
	    if ( ! $this->roles) {
	        // We use a Depth First Search approach to flatten the Roles Graph.
	        foreach ($this->getUserResource()->roles as $role) {
	            $returnValue[] = $role;
	        }
	        $returnValue = array_unique($returnValue);
	        $this->roles = $returnValue;
	    }
	    return $this->roles;
	}
}