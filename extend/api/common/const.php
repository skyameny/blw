<?php
/**
 * api常量
 * @var unknown
 */
define("CONFIGKEY_TOKEN_EXPIRE_TIME", "token_expire_time");//token有效时间key

/**
 * api模块错误码
 * @var unknown
 */
define('STATUS_API_EXCEPTION', 60100);
define("STATUS_API_TOKEN_EXPIRE", 60101); //token过期
define("STATUS_API_TOKEN_TOO_CONTINUALLY",60102);//token请求太频繁
define("STATUS_API_TOKEN_MUST_NOT_NULL", 60103);//token不能为空
define('STATUS_INVALID_APPID', 60104);//AppID无效
define('STATUS_INVALID_SECRET', 60105);//SECRET无效
define("STATUS_INVALID_TYPE",60106);
define("STATUS_API_TOKEN_WILL_BE_EXPIRED", 60107); //token即将过期
define('STATUS_AUTH_FAILED', 60108);//API请求权限
 



