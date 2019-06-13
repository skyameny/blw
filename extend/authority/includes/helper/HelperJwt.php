<?php
/**
 * Jwt帮助类 生成token
 * User: keepwin100
 * Date: 2019-06-12
 * Time: 17:04
 */

namespace authority\includes\helper;
use core\model\BlModel;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;


class HelperJwt
{
    /**
     * 创建token
     * @param BlModel $member $user
     * @return mixed
     */
    public static function buildToken(BlModel $member)
    {
        $builder = new Builder();
        $signer = new Sha256();
        $builder->setIssuer('http://www.blw.com');
        // 设置接收人
        $builder->setAudience('http://example.org');
        // 设置id
        $builder->setId($member->getAttr("username"), true);
        // 设置生成token的时间
        $builder->setIssuedAt(time());
        // 设置在60秒内该token无法使用
        $builder->setNotBefore(time() + 60);
        // 设置过期时间
        $builder->setExpiration(time() + 86400);
        // 给token设置一个id
        $builder->set('uid', $member->getAttr("id"));
        // 对上面的信息使用sha256算法签名
        $builder->sign($signer, '签名key');
        // 获取生成的token
        return $builder->getToken();
    }
}