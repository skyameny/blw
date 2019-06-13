<?php
/**
 * Created by PhpStorm.
 * User: keepwin100
 * Date: 2019-06-13
 * Time: 19:54
 */

namespace app\api\logic;

use community\service\MemberService;
use core\logic\Logic;

class MemberLogic extends Logic
{
    /**
     * @var MemberService
     */
    protected $memberService;

    public function getMembers($params)
    {
        $this->memberService = MemberService::singleton();
        $members = $this->memberService->getMembers($params);
        return $members;
    }



}