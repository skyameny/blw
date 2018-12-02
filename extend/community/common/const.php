<?php
/**
 * 社交模块
 * @var unknown
 */
define('BL_COMMUNITY_VERSION', '1.0.0 Bate');
define("GARDEN_SERVICE", "community\\service\\GardenService"); //社区服务
define("OPERATOR_SERVICE", "community\\service\\OperatorService"); //运营商服务
define("MEMBER_SERVICE", "community\\service\\MemberService"); //注册用户服务


//错误码11

defined("STATUS_CODE_COMMUNITY_NOT_FOUND") or define("STATUS_CODE_COMMUNITY_NOT_FOUND", 110101);//指定社区不存在
defined("STATUS_CODE_COMMUNITY_AREADY_EXISTS") or define("STATUS_CODE_COMMUNITY_AREADY_EXISTS", 110102);//同名社区已经存在
defined("STATUS_CODE_ON_COMMUNITY_ID") or define("STATUS_CODE_ON_COMMUNITY_ID", 110103);//指定社区ID不存在
defined("STATUS_CODE_BUILDING_NO_NAME") or define("STATUS_CODE_BUILDING_NO_NAME", 110104);//没有建筑名称
defined("STATUS_CODE_ADD_BUILDING_FAILED") or define("STATUS_CODE_ADD_BUILDING_FAILED", 110105);//添加建筑失败
defined("STATUS_CODE_BUILDING_NAME_EXISTS") or define("STATUS_CODE_BUILDING_NAME_EXISTS", 110106);//建筑名称重复
defined("STATUS_CODE_EDIT_FAMILY_FAILED") or define("STATUS_CODE_EDIT_FAMILY_FAILED", 110108);//修改失败
defined("STATUS_CODE_EDIT_BUILDING_FAILED") or define("STATUS_CODE_EDIT_BUILDING_FAILED", 110107);//修改失败
defined("STATUS_CODE_ADD_FAMILY_FAILED") or define("STATUS_CODE_ADD_FAMILY_FAILED", 110109);//修改失败
defined("STATUS_CODE_FAMILY_NO_EXISTS") or define("STATUS_CODE_FAMILY_NO_EXISTS", 110110);//修改失败















