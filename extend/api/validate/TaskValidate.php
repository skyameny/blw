<?php
namespace api\validate;

use core\validate\CoreValidate;

class TaskValidate extends CoreValidate
{
    protected $rule = [
        'page' => 'number',
        'limit' => 'number',
        'task_id' => 'number',
        'name' => 'require',
        'script_id' => 'require|number',
        'account_num' => 'require|number',
        'clues' => 'require|array',
    ];
    
    protected $message = [
        'page.number' => PARAM_TYPE_ERROR,
        'limit.number' => PARAM_TYPE_ERROR,
        'task_id.number' => PARAM_TYPE_ERROR,
        'name.require' => '任务名称不能为空',
        'script_id.require' => '话术为必选项',
        'script_id.number' => PARAM_TYPE_ERROR,
        'account_num.require' => '机器人数量为必选项',
        'account_num.number' => PARAM_TYPE_ERROR,
        'clues.require' => '请提供需要删除的号码',
        'clues.array' => PARAM_TYPE_ERROR,
    ];
    
    protected $scene = [
        'gettasklist' => ['page','limit'],
        'saveoutcalltask' => ['task_id','name','script_id','account_num'],
        'deleteclues' => ['task_id','clues'],
        'startoutcalltask' => ['task_id'],
        'stopoutcalltask' => ['task_id'],
        'deleteoutcalltask' => ['task_id'],
        'callclues' => ['task_id','clues'],
        'getcalllogs' => ['task_id','clues'],
        'gettaskdetail' => ['task_id'],
    ];
}