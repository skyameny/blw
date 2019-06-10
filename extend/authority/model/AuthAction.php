<?php
/**
 * 行为操作
 * User: keepwin100
 * Date: 2019-04-23
 * Time: 11:34
 */

namespace authority\model;

use think\Model;

class AuthAction extends Model
{
    const AUTH_ACTION_DISABLE = 0;
    const AUTH_ACTION_ENABLE  = 1;

    public  function  disable()
    {
        $this->save(["status"=>self::AUTH_ACTION_DISABLE]);
    }



}