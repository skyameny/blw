<?php
namespace core\model;

use think\Model;


class AuthAccess extends BlModel
{
    public function authRule()
    {
        return $this->belongsToMany('auth_rule');
    }
}