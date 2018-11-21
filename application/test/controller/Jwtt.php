<?php 
/*
 +-------------------------------------------------------------------------------------------
 + Title        : 代码标题
 + Version      : V1.0.0.2
 + Initial-Time : 2018年11月09日
 + @auth Dream <1015617245@qq.com>
 + Last-time    : 2018-11-09
 + Desc         : 项目描述
 +-------------------------------------------------------------------------------------------
*/

namespace app\test\controller;

use think\Controller;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;

class Jwtt extends  Controller
{
    
    public function  token()
    {
        exit("dde");exit;
        $builder = new Builder();
        $signer = new Sha256();
        $builder->setIssuer('http://example.com');
        // 设置接收人
        $builder->setAudience('http://example.org');
        // 设置id
        $builder->setId('4f1g23a12aa', true);
        // 设置生成token的时间
        $builder->setIssuedAt(time());
        // 设置在60秒内该token无法使用
        $builder->setNotBefore(time() + 60);
        // 设置过期时间
        $builder->setExpiration(time() + 3600);
        // 给token设置一个id
        $builder->set('uid', 1);
        // 对上面的信息使用sha256算法签名
        $builder->sign($signer, '签名key');
        // 获取生成的token
        $token = $builder->getToken();

        var_dump($token);
        
    }
}
