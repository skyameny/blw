<?php
/**
 * jwt认证机制
 * User: keepwin100
 * Date: 2019-06-11
 * Time: 22:26
 */
namespace authority\includes\user\storage;

use authority\includes\user\IdentifyUser;
use community\service\MemberService;
use think\Request;

class JwtAuthStorage implements AuthStorage
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
        $this->memberService->createToken($member);

    }

    public function getStorageUser()
    {
        $request = Request::instance();
        $access_token = $request->param("access_token");
    }

    public function endStorage()
    {

    }
}