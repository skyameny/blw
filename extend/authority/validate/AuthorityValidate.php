<?php
/**
 * Created by PhpStorm.
 * User: silkshadow
 * Date: 2019-04-25
 * Time: 16:32
 */

namespace authority\validate;


use aicallup\service\SystemConfigService;
use core\validate\CoreValidate;

class AuthorityValidate extends CoreValidate
{
    private $systemConfigService;

    public function __construct(array $rules = [], array $message = [], array $field = [])
    {
        parent::__construct($rules, $message, $field);
        $this->systemConfigService = app(SystemConfigService::class);
    }

    protected $rule = [
        'id' => 'require|integer',
        'ids' => 'require|isNumbers',
        // 角色
        'name' => 'require|checkLength:role_name',
        'remark' => 'checkLength:role_remark',
        'status' => 'require|in:0,1',
        'auth_rules' => 'require|isNumbers',
        // 账户
        'username' => 'require|length:8,20|checkLength:account_name',
        'password' => 'checkFormat:password',
        'mobile' =>   'mobile',
        'role_ids' => 'require|isNumbers',
        // 分页
        'page' => 'number',
        'limit' => 'number',
    ];

    protected $message = [
        'ids.isNumbers' => "ids参数为数字|数组|字符串",
    ];


    protected $scene = [
        'getrolesbywhere'   => ['page','limit'],
        'getrolebyid'       => ['id'],
        'setrolebydata'     => ['name', 'remark','status', 'auth_rules'],
        'deleterolesbyids'  => ['ids'],
        'controlrolesbyids' => ['ids','status'],
        // 账户
        'setuserbydata'     => ['mobile','role_ids'],
        'getusersbywhere'   => ['page','limit'],
        'resetpasswordbyid'=> ['id','password'],
        'deleteusersbyids'  => ['ids']
    ];

    /**
     * @author wangyifen
     * @description 根据配置项进行长度校验
     */
    protected function checkLength($value, $rule, $data)
    {
        $lenMap = [
            'role_name' => [
                'key' => 'role_title_length_limit',
                'name' => '角色名称'
            ],
            'role_remark' => [
                'key' => 'role_remark_length_limit',
                'name' => '角色描述'
            ],
            'account_name' => [
                'key' => 'account_title_length_limit',
                'name' => '账号名称'
            ]
        ];
        $limit = $this->systemConfigService->getConfig($lenMap[$rule]['key']);
        $name = $lenMap[$rule]['name'];

        if(mb_strlen($value) > $limit) {
            return $name."长度不能超过$limit"."个字符";
        }
        return true;
    }

    // TODO: 格式校验
    protected function checkFormat($value, $rule){
        // username: 用户名称只能是大小写英文、数字和下划线
        // password: 密码8-20个字符，规定必须有大小写英文数字和特殊符号
        return true;
    }


    protected function isNumbers($value)
    {
        if (is_string($value)) {
            $value = trim($value, ",");
        }

        $arr = is_array($value) ? $value : (strpos($value, ",") === false) ? (array) $value : explode(",", $value);

        foreach ($arr as $k => $v) {
            if (! is_numeric($v)) {
                return false;
            }
        }
        return true;
    }


    function mobile($mobile)
    {
        if (!is_numeric($mobile)) {
            return false;
        }
        return preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $mobile) ? true : false;
    }

}