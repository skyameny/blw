<?php
namespace core\model;

use think\Model;


class AuthAccess extends Model
{
    public function authRule()
    {
        return $this->belongsToMany('auth_rule');
    }
}