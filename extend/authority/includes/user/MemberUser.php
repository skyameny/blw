<?php
/**
 * 用户权限
 *
 * 管理员
 * session用户
 */
namespace authority\includes\user;

use community\model\Member as UserModel;
use community\model\Community;


class MemberUser implements IdentifyUser
{

    protected $roles = array();

    protected $identifier= 0;

    protected $userResource;

    protected $gardenResource; //社区

    protected $cache;

    protected $cachedProperties = array(
        "username",
        "gid",
        "mobile",
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
    public function getGardenResource()
    {
        return Community::get($this->getPropertyValues("gid"));
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