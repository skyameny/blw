<?php
/**
 * 系统错误码
 * 采用6位错误码
 * 前两位表示模块 
 * 10 => core;
 * 11 =>community
 * 12 =>maintenance
 * 13 =>artical
 * 14 =>active 
 * 15 =>im
 * 16 =>shop
 * ******请严格遵守本约定*****
 */

return [
    ERROR_PREFIX . STATUS_CODE_SYSTEM_ERROR => "系统错误",
    ERROR_PREFIX . STATUS_CODE_PARAM_ERROR => "参数错误",
    ERROR_PREFIX . STATUS_CODE_PARAM_TYPE_ERROR => "参数类型错误",
    ERROR_PREFIX . STATUS_CODE_PARAM_COUNT_ERROR => "参数数量错误",
    ERROR_PREFIX . STATUS_CODE_ILLEGAL_REQUEST => "非法请求",
    ERROR_PREFIX . STATUS_CODE_ILLEGAL_OPRATION => "非法操作",
    ERROR_PREFIX . STATUS_CODE_FILE_UPLOAD_ERROR => "文件错误",
    ERROR_PREFIX . STATUS_CODE_OPRATION_FAILED => "操作失败",
    ERROR_PREFIX . STATUS_CODE_NOT_SUPPORT => "暂不支持此项操作",
    
    //setting
    ERROR_PREFIX . STATUS_CODE_SETTING_FAILED => "配置失败",
    ERROR_PREFIX . STATUS_CODE_SETTING_READONLY =>"该配置无法修改",
    ERROR_PREFIX . STATUS_CODE_SETTING_NAME_ERROR => "配置键名为数字、字母_且不超过32位",
    ERROR_PREFIX . STATUS_CODE_SETTING_NOT_DELETEABLE => "该配置不能删除",
    ERROR_PREFIX . STATUS_CODE_SETTING_NOT_EXISTS => "该配置不存在",
    
];