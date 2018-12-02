<?php
namespace  core\exception;

class SettingException extends CommonException
{
    protected $status_code = STATUS_CODE_SETTING_FAILED; //配置失败
}