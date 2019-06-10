<?php
/**
 * Created by PhpStorm.
 * User: keepwin100
 * Date: 2019-06-02
 * Time: 12:42
 */
 namespace authority\includes\identify;

 use authority\includes\user\IdentifyUser;

 interface Identify
 {
     /**
      * 获取认证用户
      * @return IdentifyUser
      */
     public function getIdentifyUser();

     /**
      * @return mixed
      */
     public function refresh();

 }