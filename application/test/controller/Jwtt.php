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

use identify\Controller\Jwt;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Key;
use think\Controller;

include VENDOR_PATH."/autoload.php";

class Jwtt extends  Controller
{
    public function  token() 
    {
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
        var_dump($token->getPayload());
    }

    public function t1()
    {
        $time = time();
        $token = (new Builder())->issuedBy('http://example.com') // Configures the issuer (iss claim)
        ->permittedFor('http://example.org') // Configures the audience (aud claim)
        ->identifiedBy('4f1g23a12aa', true) // Configures the id (jti claim), replicating as a header item
        ->issuedAt($time) // Configures the time that the token was issue (iat claim)
        ->canOnlyBeUsedAfter($time + 60) // Configures the time that the token can be used (nbf claim)
        ->expiresAt($time + 3600) // Configures the expiration time of the token (exp claim)
        ->withClaim('uid', 1) // Configures a new claim, called "uid"
        ->getToken(); // Retrieves the generated token


        $token->getHeaders(); // Retrieves the token headers
        $token->getClaims(); // Retrieves the token claims

     #   echo $token->getHeader('jti'); // will print "4f1g23a12aa"
     #   echo $token->getClaim('iss'); // will print "http://example.com"
     #   echo $token->getClaim('uid'); // will print "1"
        echo $token; // The string representation of the object is a JWT string (pretty easy, right?)

    }


    function t2()
    {
        $signer = new Sha256();
        $time = time();

        $token = (new Builder())->issuedBy('http://example.com') // Configures the issuer (iss claim)
        ->permittedFor('http://example.org') // Configures the audience (aud claim)
        ->identifiedBy('4f1g23a12aa', true) // Configures the id (jti claim), replicating as a header item
        ->issuedAt($time) // Configures the time that the token was issue (iat claim)
        ->canOnlyBeUsedAfter($time + 60) // Configures the time that the token can be used (nbf claim)
        ->expiresAt($time + 3600) // Configures the expiration time of the token (exp claim)
        ->withClaim('uid', 1) // Configures a new claim, called "uid"
        ->getToken($signer, new Key('testing')); // Retrieves the generated token


        var_dump($token->verify($signer, 'testing 1')); // false, because the key is different
        var_dump($token->verify($signer, 'testing')); // true, because the key is the same
    }

    function parse()
    {
        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiIsImp0aSI6ImtlZXB3aW4xMDAifQ.eyJpc3MiOiJodHRwOlwvXC93d3cuYmx3LmNvbSIsImF1ZCI6Imh0dHA6XC9cL2V4YW1wbGUub3JnIiwianRpIjoia2VlcHdpbjEwMCIsImlhdCI6MTU2MDM5MDAwMCwibmJmIjoxNTYwMzkwMDYwLCJleHAiOjE1NjA0NzY0MDAsInVpZCI6MX0.mrv1kpB9';
        $token = (new Parser())->parse((string) $token); // Parses from a string
        $token->getHeaders(); // Retrieves the token header
        $token->getClaims(); // Retrieves the token claims

        echo $token->getHeader('jti'); // will print "4f1g23a12aa"
        echo $token->getClaim('iss'); // will print "http://example.com"
        echo $token->getClaim('uid'); // will print "1"
    }


}
