<?php
/**
 * Created by PhpStorm.
 * User: keepwin100
 * Date: 2019-06-13
 * Time: 10:39
 */

namespace authority\includes\user\storage;


use authority\includes\user\AnonymousUser;
use authority\includes\user\IdentifyUser;
use authority\includes\user\MemberUser;
use community\service\MemberService;
use core\exception\CoreException;
use think\Request;

class DtAuthStorage implements AuthStorage
{
    /**
     * @var MemberService
     */
    protected $memberService;

    public function __construct()
    {
        $this->memberService = MemberService::singleton();
    }

    public function startStorage(IdentifyUser $identifyUser)
    {
        $member = $identifyUser->getUserResource();
        $this->memberService->refreshToken($member);
        return true;

    }

    /**
     * 获取当前登陆用户
     * @return AnonymousUser|MemberUser|mixed
     * @throws \authority\exception\MemberException
     */
    public function getStorageUser()
    {
        $request = Request::instance();
        $access_token = $request->header("access_token");
        $mid = $request->header("member_mid");
        if(empty($mid) ||empty($access_token)){
            return new AnonymousUser();
        }
        $members = $this->memberService->getMembers(["id"=>$mid]);
        if(!empty($members)){
            $member = current($members);
            $res = $this->memberService->verify($member,$access_token);
            if($res){
                return new MemberUser($member);
            }
        }
        return new AnonymousUser();
    }

    public function endStorage()
    {

    }
}