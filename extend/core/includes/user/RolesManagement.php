<?php
/**
 * @author Dream
 * 角色管理
 */
namespace core\includes\user;

use core\models\BlModel;

interface RolesManagement
{
    /**
     * 添加角色
     */
    public function addRole($label, $includedRoles = null, BlModel $class = null);

    /**
     * 删除角色
     * @param core_kernel_classes_Resource $role
     */
    public function removeRole(BlModel $role);

    /**
     * 
     * @param core_kernel_classes_Resource $role
     */
    public function getIncludedRoles(BlModel $role);

    /**
     * 角色
     * @param core_kernel_classes_Resource $role
     * @param core_kernel_classes_Resource $roleToInclude
     */
    public function includeRole(BlModel $role, array $roleToInclude);
    
    /**
     * Uninclude a Role from another Role.
     * 
     */
    public function unincludeRole(BlModel $role,  $roleToUninclude);
    
    /**
     * 获取角色
     */
    public function getAllRoles();

}

?>