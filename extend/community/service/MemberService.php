<?php
/**
 * 会员服务
 * User: keepwin100
 * Date: 2019-06-12
 * Time: 13:52
 */

namespace community\service;

use authority\exception\MemberException;
use authority\includes\helper\HelperPassword;

use community\model\Member;
use community\model\MemberToken;
use core\service\Service;
use core\utils\ExLog;


class MemberService extends Service
{
    /**
     * @var Member
     */
    protected $memberModel;

    protected function __construct()
    {
        $this->memberModel = new Member();
        parent::__construct();
    }

    /**
     * 获取密码加密对象
     * @return HelperPassword
     */
    public function getPasswordHash()
    {
        $helper = new HelperPassword("sha1", 5);
        return $helper;
    }

    /**
     * APP创建用户token
     * 该操作会顶替原有token
     * @param Member $member
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function refreshToken(Member $member)
    {
        return $member->createToken();
    }

    /**
     * 判断用户token是否合法
     * @param $member
     * @param $token
     */
    public function verify(Member $member,$token)
    {
        $tokenModel = $member->getToken();
        if (empty($tokenModel)) {
            ExLog::log("用户尚未注册[".$member->getAttr("username")."]");
            throw new MemberException(STATUS_CODE_USER_NOT_EXITS);
        }
        if(!$tokenModel->isMatch($token)){
            ExLog::log("用户token不正确");
            throw new MemberException(STATUS_CODE_TOKEN_INVALID);
        }
        if ($tokenModel->is_expired()) {
            throw new MemberException(STATUS_CODE_USER_NOT_EXITS);
        }
        return true;
    }

    /**
     * @param Member $member
     * @return mixed
     * @throws MemberException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getMemberToken(Member $member)
    {
        $token = $member->getToken();
        if (empty($token)) {
            ExLog::log("不存在该用户");
            throw new MemberException(STATUS_CODE_USER_NOT_EXITS);
        }
        return $token;
    }

    /**
     * 查询用户列表
     * @param array $condition
     * @return array|mixed
     */
    public function getMembers($condition = [])
    {
        return $this->memberModel->searchInstances($condition);
    }

    public function loginExists($username, $gid)
    {
        $members = $this->getMembers(["username" => $username, "gid" => $gid]);
        return !!$members;
    }

    public function mobileExists($mobile, $gid)
    {
        $members = $this->getMembers(["username" => $mobile, "gid" => $gid]);
        return !!$members;
    }

    /**
     * @param $username
     * @param $pwd
     * @param $mobile
     * @param int $gid
     * @return User
     */
    public function addMember($username, $pwd, $mobile, $gid)
    {
        if (!empty($username) && $this->loginExists($username, $gid)) {
            throw new MemberException(STATUS_CODE_LOGIN_EXITS);
        }
        if (!empty($mobile) && $this->mobileExists($mobile, $gid)) {
            throw new MemberException(STATUS_CODE_MOBILE_EXITS);
        }
        $user_model = new Member();
        $flag = $user_model->save([
            "username" => $username,
            "passwd" => $pwd,
            "mobile" => $mobile,
            "status" => Member::STATUS_ENABLE,
            "nickname" => $username,
            "create_time" => NOW_TIME,
            "gid" => $gid
        ]);
        if (!$flag) {
            throw new UserException(STATUS_CODE_USER_ADD_FAILED);
        }
        return $user_model;
    }


}