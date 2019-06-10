<?php
/**
 * 用户-角色服务
 */

namespace core\service;

use core\includes\helper\HelperPassword;
use core\model\User;
use core\exception\UserException;
use core\includes\user\UserManagement;
use core\includes\user\RoleManagement;
use core\includes\session\SessionManagement;
use think\Model;
use core\model\Role;
use think\Exception;

/**
 * The UserService
 *
 * @access public
 * @author Dream
 * @core
 */
class UserService implements UserManagement, RoleManagement
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
     * 返回helperPassword
     * @return HelperPassword
     */
    public static function getPasswordHash()
    {
        return new HelperPassword(
            defined('PASSWORD_HASH_ALGORITHM') ? PASSWORD_HASH_ALGORITHM : self::LEGACY_ALGORITHM,
            defined('PASSWORD_HASH_SALT_LENGTH') ? PASSWORD_HASH_SALT_LENGTH : self::LEGACY_SALT_LENGTH
        );
    }

    public function disable(model $user)
    {
        // TODO: Implement disable() method.
    }

    public function enable(model $user)
    {
        // TODO: Implement enable() method.
    }

    /**
     * @param $login
     * @param Model|null $class
     * @return bool|mixed
     */
    public function loginExists($login, Model $class = null)
    {
        $returnValue = (bool)false;
        if (is_null($class)) {
            $class = new User();
        }
        $users = $class->searchInstances(
            array("username" => $login)
        );

        if (count($users) > 0) {
            $returnValue = true;
        }

        return (bool)$returnValue;
    }

    /**
     * 验证手机号是否被注册
     * @param $mobile
     * @param User|null $class
     * @return bool
     */
    public function mobileExists($mobile, User $class = null)
    {
        $returnValue = (bool)false;

        if (is_null($class)) {
            $class = new User();
        }
        $users = $class->searchInstances(
            array("mobile" => $mobile)
        );

        if (count($users) > 0) {
            $returnValue = true;
        }

        return (bool)$returnValue;
    }

    /**
     * 添加用户
     * @param array $data
     * @param Model|null $role
     * @param Model|null $class
     * @return false|int|mixed|null
     * @throws UserException
     */
    public function addUser($data, Model $role = null, Model $class = null)
    {
        $returnValue = null;
        $login = $data["username"];
        $mobile = $data["mobile"];

        if ($this->loginExists($login) || $this->mobileExists($mobile)) {
            throw new UserException("Login '${login}' already in use.", LOGIN_EXITS);
        } else {
            $role = (empty($role)) ? new Role(INSTANCE_ROLE_GENERIS) : $role;
            $user = (!empty($class)) ? $class : new User();
            $returnValue = $user->save(array(
                "username" => $login,
                "mobile" => $mobile,
                "nickname" => empty($data["nickname"]) ? $login : $data["nickname"],
                "passwd" => self::getPasswordHash()->encrypt($data["passwd"]),
                "create_time" => NOW_TIME,
                "eid" => $data["eid"],
                #  "avatar" => empty($data["user_avatar"]) ? DEFAULT_USER_AVATAR : $data["user_avatar"],
                "type" => $data["type"],
                "status" => User::STATUS_ENABLE,
            ));
            if (empty($returnValue)) {
                throw new UserException("Unable to create user with login = '${login}'.", USER_ADD_FAILED);
            }
            if ($role instanceof Role) {
                $this->attachRole($user, $role);
            }
        }
        //返回用户ID
        return $returnValue;
    }

    /**
     * 删除用户
     * @param Model $user
     * @return bool
     */
    public function removeUser(Model $user)
    {
        $returnValue = (bool)false;
        $roles = $this->getUserRoles($user);
        if (!is_null($roles)) {
            foreach ($roles as $role) {
                $this->unnatachRole($user, $role);
            }
        }
        $returnValue = $user->delete();

        return (bool)$returnValue;
    }

    /**
     * 获取用户名所对应的用户
     * @param $login
     * @param User|null $class
     * @return User|mixed|null
     */
    public function getOneUser($login, User $class)
    {
        $returnValue = null;

        if (empty($class)) {
            $class = new User();
        }
        $users = $class->searchInstances(
            array("username" => $login)
        );

        if (count($users) == 1) {
            $returnValue = current($users);
        } else if (count($users) > 1) {
            $msg = "More than one user have the same login '${login}'.";
        }

        return $returnValue;
    }


    public function isASessionOpened()
    {
        return !SessionManagement::isAnonymous();
    }

    /**
     * 密码是否合法
     * @param $password
     * @param Model $user
     * @return bool
     * @throws UserException
     */
    public function isPasswordValid($password, Model $user)
    {
        $returnValue = (bool)false;

        if (!is_string($password)) {
            throw new UserException('The password must be of "string" type, got ' . gettype($password));
        }

        $hash = $user->getAttr("passwd");
        $returnValue = self::getPasswordHash()->verify($password, $hash);

        return (bool)$returnValue;
    }

    /**
     * 设置密码
     * @param Model $user
     * @param $password
     * @throws UserException
     */
    public function setPassword(Model $user, $password)
    {
        if (!is_string($password)) {
            throw new UserException('The password must be of "string" type, got ' . gettype($password));
        }
        $user->setAttr("passwd", self::getPasswordHash()->encrypt($password));
        $user->save();
    }

    /**
     * 获取用户的角色
     *
     * @access public
     * @author Dream
     * @param  Resource user A Generis User.
     * @return array
     */
    public function getUserRoles(Model $user)
    {
        $roles = $user->getAttr('roles');
        return $roles;
    }

    /**
     * @param Model $user
     * @param $role
     * @return bool
     * @throws UserException
     */
    public function userHasRoles(Model $user, $role)
    {
        $returnValue = (bool)false;

        if (empty($role)) {
            throw new UserException('The $roles parameter must not be empty.');
        }
        $roles = $user->getAttr("roles");
        foreach ($roles as $c_role) {
            if ($c_role->isEqual($role)) {
                return true;
            }
        }
        return (bool)$returnValue;
    }

    /**
     * @param Model $user
     * @param Model $role
     * @throws UserException
     */

    public function attachRole(User $user, Model $role)
    {
        return $user->roles()->save($role);
    }

    /**解除绑定
     * @param Model $user
     * @param Model $role
     * @return bool
     */
    public function unnatachRole(Model $user, Model $role)
    {
        $flag = $user->roles()->detach($role->getAttr("id"));
        //ExLog::log("解除用户与角色的关系：".Db::getLastSql(),ExLog::DEBUG);
        return boolval($flag);
    }

    /**
     * 创建角色
     * @param $title
     * @param string $description
     * @param int $eid
     * @param int $type
     * @return mixed|void
     */
    public function addRole($title, $description = "", $eid = 0, $type = 0)
    {
        $role = new Role();
        $roleData = [];
        $roleData["name"] = $title;
        $roleData["remark"] = $description;
        $roleData["gid"] = $eid;
        $roleData["type"] = $type;
        return $role->save($roleData);
    }

    /**
     * 删除角色
     *
     * @param Model $role
     * @return bool
     */
    public function removeRole(Model $role)
    {
        $returnValue = $role->remove();
        return (bool)$returnValue;
    }

    /**
     * @param Model $role
     */
    public function getIncludedRoles(Model $role)
    {
        //
    }

    /**
     * 获取允许的role
     */
    public function getAllowedRoles()
    {

    }

    /**
     * 系统默认角色
     *
     * @return Role|null
     * @throws \think\exception\DbException
     */
    public function getDefaultRole()
    {
        $returnValue = null;

        $returnValue = Role::get(Role::DEFAULT_ROLE);

        return $returnValue;
    }

    /**
     * @param Model $role
     * @param array $roleToInclude
     */
    public function includeRole(Model $role, array $roleToInclude)
    {
        //
    }

    /**
     * @param Model $role
     * @param $roleToUninclude
     */
    public function unincludeRole(Model $role, $roleToUninclude)
    {

    }

    /**
     * 登录
     * @param $login
     * @param $password
     * @param $allowedRoles
     * @return bool
     */
    public function login($login, $password, $allowedRoles)
    {
        return LoginService::login($login, $password);
    }


    private function __construct()
    {
        // Only to restrict instances of this class to a single instance.
    }


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
     * 退出
     * @return bool
     */
    public function logout()
    {
        return SessionManagement::endSession();
    }

    /**
     * @param array $condition
     * @return Role[]|false
     * @throws \think\exception\DbException
     */
    public function getAllRoles($condition = [])
    {
        return Role::all($condition);
    }



    ##扩展UserService

    /**
     * 获取具有角色的用户
     */
    public function getUsersByRole()
    {
        //do something
    }

    /**
     * 获取所有用户
     * @param array $filters
     * @return array
     */
    public function getAllUsers( $filters = [])
    {
        $userClass = new User();
        return (array) $userClass->searchInstances($filters);
    }
}
