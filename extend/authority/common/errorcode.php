<?php
return [
    ERROR_PREFIX . LOGIN_EXITS => '用户名已存在',
    ERROR_PREFIX . USER_ADD_FAILED => '用户添加失败',
    ERROR_PREFIX . USER_DELETE_FAILED => '用户删除失败',
    ERROR_PREFIX . PASSWD_TYPE_ERROR => '用户密码格式错误',
    ERROR_PREFIX . STATUS_CODE_LOGIN_FAILED => '用户名或密码错误',
    ERROR_PREFIX . LOGIN_FAILED_FIVE => '用户名或密码错误，若输入错误10次，今日将无法登录',
    ERROR_PREFIX . MULTIPLE_USERS_FOR_SAME_LOGIN => '多个同名用户',
    ERROR_PREFIX . SYS_ADMIN_CAN_NOT_DEL => "系统管理员不能删除",
    ERROR_PREFIX . EP_ADMIN_CAN_NOT_DEL => "企业管理员不能删除",
    ERROR_PREFIX . STATUS_CODE_USER_NOT_FOUND => "用户不存在",
    ERROR_PREFIX . USER_SAVE_FAILED => "用户保存失败",
    ERROR_PREFIX . USER_BEYOND_COUNT_LIMIT => '用户添加总数超限',
    ERROR_PREFIX . MOBILE_EXITS => '该手机号已被注册',
    ERROR_PREFIX . EP_ADMIN_ROLE_CAN_NOT_EDIT => '企业管理员角色不可绑定',

    ERROR_PREFIX . ROLE_REMOVE_FAILED => '角色删除失败',
    ERROR_PREFIX . DEL_THIS_ROLE_FAILED => "删除该角色失败",
    ERROR_PREFIX . ROLE_NOT_EXSIT => '角色不存在',
    ERROR_PREFIX . USER_ROLES_CAN_NOT_DEL => '不能删除含有用户的角色',
    ERROR_PREFIX . ROLE_BEYOND_COUNT_LIMIT => '角色添加总数超限',
    ERROR_PREFIX . ROLE_NAME_EXITS => '该角色名已存在',

        //user
    ERROR_PREFIX . STATUS_CODE_AUTH_FAILED => "用户鉴权失败",
    ERROR_PREFIX . STATUS_CODE_LOGIN_FAILED => "用户名密码错误",
    ERROR_PREFIX . STATUS_CODE_LOGIN_EXITS => "用户已经存在",
    ERROR_PREFIX . STATUS_CODE_USER_ADD_FAILED => "用户添加失败",
    ERROR_PREFIX . STATUS_CODE_SESSION_TIMEOUT=> "登录会话过期",
    ERROR_PREFIX . STATUS_CODE_PERMISSION_DEND => "用户没有访问权限",
    ERROR_PREFIX . STATUS_CODE_ADD_ROLE_FAILED => "添加角色失败",
    ERROR_PREFIX . STATUS_CODE_ROLE_NAME_EXISTS => "角色名称已经存在",
];