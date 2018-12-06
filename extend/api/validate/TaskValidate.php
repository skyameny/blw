<?php
namespace api\validate;

use core\validate\CoreValidate;

class TaskValidate extends CoreValidate
{
    protected $rule = [
        'page' => 'number',
        'limit' => 'number',
        'task_id' => 'require|number',
        'task_name' => 'require',
        'script_id' => 'require|number',
        'account_num' => 'require|number',
        'clues' => 'require|array',
        'uuid' => 'require',
        'var_keys' => 'array',
        'call_number' => 'array',
    ];
    
    protected $message = [
        'page.number' => '分页参数必须为数字',
        'limit.number' => '分页参数必须为数字',
        'task_id.require' => '任务ID为必填项',
        'task_id.number' => '任务ID必须为数字',
        'task_name.require' => '任务名称不能为空',
        'script_id.require' => '话术为必选项',
        'script_id.number' => '话术ID必须为数字',
        'account_num.require' => '机器人数量为必选项',
        'account_num.number' => '机器人数量必须为数字',
        'clues.require' => '请提供号码列表',
        'clues.array' => '线索参数必须为数组',
        'uuid.require' => 'uuid不能为空',
        'var_keys.array' => '自定义字段列表必须为数组',
        'call_number.array' => '主叫号码列表必须为数组',
    ];
    
    protected $scene = [
        'gettasklist' => ['page','limit'],
        'saveoutcalltask' => ['task_name','script_id','account_num','uuid','call_number'],
        'deleteclues' => ['task_id','clues'],
        'startoutcalltask' => ['task_id'],
        'pauseoutcalltask' => ['task_id'],
        'deleteoutcalltask' => ['task_id'],
        'recallclues' => ['task_id','clues'],
        'getcallrecorddetail' => ['task_id','clues','page','limit'],
        'importclues' => ['task_id','clues','var_keys'],
        'gettaskdetail' => ['task_id'],
    ];
}