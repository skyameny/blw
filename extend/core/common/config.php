<?php 

// 调试模式开关
if(!defined("APP_DEBUG")) define("APP_DEBUG", false);

return [
    #account 主题路径
    "default_theme_path" => "themes/",
    #管理员主题
    "user_admin_theme" => "bootstrap_admin/",
    #用户默认主题
    "user_default_theme" => "default/",//"angulr_account/",
    #用户基本信息字段
    "member_base_feilds" =>["name","id","sex","level","star","avator"],
    #用户基本配置
    "default_layout"=>[
        "navbar" => "navbar-light bg-white",
        "sidebar" => "sidebar-dark-primary",
        "brand_logo" => "",
        "sidebar_open" =>"sidebar-open",
    ],
    // 错误级别
    'log_level' => [
        'log',
        'error',
        'notice',
        'alert',
        'info',
        'debug'
    ],
    // 错误级别
    'api_pagination' =>
    ["page"=>"page","limit"=>"limit","keywords"=>"search","sort"=>"sort"]
    ,
];



?>