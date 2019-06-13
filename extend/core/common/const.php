<?php
/**
 * 客户端类型
 * 1 website
 * 2 app
 * 3 cli
 * @var unknown
 */
define("NOW_TIME", time());
define('BL_CORE_VERSION', '1.0.0 Bate');
define('SESSION_NAMESPACE', 'blw_base_session');
define('GENERIS_SESSION_NAME','bl_User_QEEECr8F');
define('INSTANCE_ROLE_GENERIS','2');//默认角色

define("DEFAULT_USER_AVATAR","dist/img/1.jpg");

define("SYSTEM_SERVICE", "core\\service\\SystemService"); //系统服务

define("CLIENT_WEBSITE", 1);
define("CLIENT_APP", 2);
define("CLIENT_CLI", 3);
define("DEFAULT_LANG","zh-CN");//默认语言
define("SYSTEM_DEFAULT_GID","0");//默认语言


#the time zone, required since PHP7
define('TIME_ZONE','UTC');

define("SYSTEM_DEFAULT_CID", 0);

// configkey

define("PROPERTY_USER_TIMEZONE", "default_user_timezone");


//error cord
//====系统错误 10  00  xx
define('STATUS_CODE_SYSTEM_ERROR', 999999); // 系统错误
define('STATUS_CODE_SUCCESS', 0); // 请求成功
define("STATUS_CODE_PARAM_ERROR", 100000); // 参数错误 
define("STATUS_CODE_PARAM_TYPE_ERROR", 100001); // 参数类型错误
define("STATUS_CODE_PARAM_COUNT_ERROR", 100002); // 参数数量错误
define("STATUS_CODE_ILLEGAL_REQUEST", 100003); // 非法请求
define("STATUS_CODE_ILLEGAL_OPRATION", 100004); // 非法操作
define('STATUS_CODE_FILE_UPLOAD_ERROR', 100005);//文件错误
define('STATUS_CODE_OPRATION_FAILED', 100006);//操作失败
define('STATUS_CODE_NOT_SUPPORT', 100007);//暂不支持此项操作

//config
defined("STATUS_CODE_SETTING_FAILED") or define("STATUS_CODE_SETTING_FAILED", 100301);//设置失败
defined("STATUS_CODE_SETTING_NAME_ERROR") or define("STATUS_CODE_SETTING_NAME_ERROR", 100302);//设置名称非法
defined("STATUS_CODE_SETTING_READONLY") or define("STATUS_CODE_SETTING_READONLY", 100303);//配置为只读
defined("STATUS_CODE_SETTING_NOT_DELETEABLE") or define("STATUS_CODE_SETTING_NOT_DELETEABLE", 100304);//配置不能删除
defined("STATUS_CODE_SETTING_NOT_EDITABLE") or define("STATUS_CODE_SETTING_NOT_EDITABLE", 100305);//配置不能修改
defined("STATUS_CODE_SETTING_NOT_EXISTS") or define("STATUS_CODE_SETTING_NOT_EXISTS", 100306);//配置不存在


//11业务相关
define('AUTHOR_FAILED', 100110);//鉴权失败
define('INVALID_TOKEN', 100111);//token 无效

//207:短信验证码
define("STATUS_CODE_OVER_MAX_LIMIT_SEND_CODE", 20701);// 超过短信验证码最大限制
define("STATUS_CODE_SEND_VCODE_FAILED", 20702);// 短信发送失败
define("STATUS_CODE_VCODE_NOT_MATCH", 20703);// 验证码不正确










