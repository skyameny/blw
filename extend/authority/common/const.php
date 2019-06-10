<?php 
/**
* 模块常量定义
* @date: 2018年10月10日 下午2:37:21
* @author: wangjingfu@emicnet.com
* core:1 identity:2 oc:3 aicall:4 aicallup:5 ops:6
*/


### 常量定义
define('DEFAULT_AGENT_ROLE_ID', 2); //默认代理的角色ID
//代码底层错误 Rbac
define("STATUS_CODE_USER_FAILED", 100100);//用户错误
define('STATUS_CODE_AUTH_FAILED', 100201);//用户鉴权失败
define('STATUS_CODE_SESSION_TIMEOUT', 100202);//登录session过期
define("STATUS_CODE_LOGIN_EXITS", 100102);//用户名已经存在
define("STATUS_CODE_USER_ADD_FAILED", 100103);//用户添加失败
define("STATUS_CODE_LOGIN_FAILED", 100104);//用户名密码错误
define("STATUS_CODE_MOBILE_EXITS", 100105);//手机号已经存在
define("STATUS_CODE_NONSTANDARD_PASSWORD", 100106);//密码不规范

define("STATUS_CODE_USER_DELETE_FAILED", 100110);//用户删除存在
define("STATUS_CODE_PERMISSION_DEND", 100112);//用户没有访问权限
define("STATUS_CODE_ADD_ROLE_FAILED", 100113);//添加角色失败
define("STATUS_CODE_ROLE_NAME_EXISTS", 100114);//角色名称已经存在
define("STATUS_CODE_ROLE_NOT_EXISTS", 100115);//角色不存在
define("STATUS_CODE_ROLE_EDIT_DISABLE", 100116);//角色不能编辑
define("STATUS_CODE_ROLE_FAILED", 100200);//角色错误

define("STATUS_CODE_LOGIN_AUTH_FAILED", 100220);//登录验证错误



defined("STATUS_CODE_PASSWD_TYPE_ERROR") or define("STATUS_CODE_PASSWD_TYPE_ERROR", 100105);//用户密码格式不正确
defined("STATUS_CODE_ROLE_REMOVE_FAILED") or define("STATUS_CODE_ROLE_REMOVE_FAILED", 100106);//角色删除失败
defined("STATUS_CODE_ROLE_NOT_EXSIT") or define("STATUS_CODE_ROLE_NOT_EXSIT", 100107);//角色不存在
defined("STATUS_CODE_SESSION_TIMEOUT") or define("STATUS_CODE_SESSION_TIMEOUT", 100109);//session失效
//202:用户类
define("LOGIN_EXITS", 20201);//用户名已经存在
define("USER_ADD_FAILED", 20202);//用户添加失败
define("USER_DELETE_FAILED", 20203);//用户删除失败
define("PASSWD_TYPE_ERROR", 20204);//用户密码格式不正确
define("MULTIPLE_USERS_FOR_SAME_LOGIN", 20206);//多个同名用户
define("SYS_ADMIN_CAN_NOT_DEL", 20207);//系统管理员不能删除
define("STATUS_CODE_USER_NOT_FOUND", 20208);//用户不存在
define("LOGIN_FAILED_FIVE", 20209);//用户登录错误第五次
define("USER_SAVE_FAILED", 20210);//用户保存失败
define("USER_BEYOND_COUNT_LIMIT", 20211);// 新建用户超出限制
define("MOBILE_EXITS", 20212);// 手机号已被注册
define("EP_ADMIN_CAN_NOT_DEL", 20213);//企业管理员不能删除
define("EP_ADMIN_ROLE_CAN_NOT_EDIT", 20214);

//203:角色类
define("ROLE_REMOVE_FAILED", 20301);//角色删除失败
define("ROLE_NOT_EXSIT", 20302);//角色不存在
define("USER_ROLES_CAN_NOT_DEL", 20303);//不能删除含有用户的角色
define("DEL_THIS_ROLE_FAILED", 20304);//删除该角色失败
define("ROLE_BEYOND_COUNT_LIMIT", 20305);// 新建角色超出限制
define("ROLE_NAME_EXITS", 20306);// 该角色名已存在



