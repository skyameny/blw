<?php 
/*
 +-------------------------------------------------------------------------------------------
 + Title        : 社区关联表
 + Version      : V1.0.0.2
 + Initial-Time : 2018年11月09日
 + @auth Dream <1015617245@qq.com>
 + Last-time    : 2018-11-09
 + Desc         : 项目描述
 +-------------------------------------------------------------------------------------------
*/


namespace community\model;

use core\model\BlModel;

class CommunityProfile extends BlModel
{
    protected $likeColumn = ["address","name"];
}