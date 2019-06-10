<?php
/**
 * Created by PhpStorm.
 * User: silkshadow
 * Date: 2019-04-25
 * Time: 15:23
 */

namespace authority\dao;

use core\dao\Dao;
use authority\model\AuthRule;


class AuthorityDao extends Dao
{
    protected $class = AuthRule::class;

    protected $defaultSort = ['order'];

    protected $authRuleVisible = ['id', 'parent_id', 'name', 'title'];


    public function __construct()
    {
        $this->class = new AuthRule();
    }

    public function getAuthorityTreeByWhere($where = [])
    {
        $menus = $this->getAllMenus($where);
        return $this->getAuthorityByParentId($menus);
    }

    public function getAllMenus($where){
        $where['status'] = AuthRule::STATUS_USEDABLED;
        return $this->findByWhere($where);
    }

    public function getAuthorityByParentId($menus, $pid = AuthRule::TOP_LEVEL){
        $tree = array();

        foreach ($menus as $menu){
            if($menu['parent_id'] == $pid){
                $menu = $menu->visible($this->authRuleVisible)->toArray();
                $child = $this->getAuthorityByParentId($menus, $menu['id']);
                $menu['children'] = $child;
                $tree[] = $menu;
            }
        }

        return $tree;
    }

}