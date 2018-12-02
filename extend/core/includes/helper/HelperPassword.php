<?php
namespace core\includes\helper;

use core\exception\PasswordException;

/**
 * Password Hash class.
 * 
 * An helper class focusing on password validation/generation.
 */
class HelperPassword
{
    const MAX_PASSWORD_LIMIT = 20;
    const MIN_PASSWORD_LIMIT = 6;
    
    private $algorithm;
    private $saltLength;

    public function __construct($algorithm, $saltLength) {
        $this->algorithm = $algorithm;
        $this->saltLength = $saltLength;
    }

    /**
     * 加密
     * @param $password
     *            
     * @return string
     * @throws PasswordConstraintsException
     */
    public function encrypt($password)
    {
        if (self::validate($password)) {
            $salt = HelperRandom::generateString($this->getSaltLength());
            return $salt . hash($this->getAlgorithm(), $salt . $password);
        }
        return "";
    }

    public function verify($password, $hash) 
    {
      
        $salt = substr($hash, 0, $this->getSaltLength());
        $hashed = substr($hash, $this->getSaltLength());
        $result =  hash($this->getAlgorithm(), $salt.$password) === $hashed;
        return $result;
    }

    protected function getAlgorithm()
    {
        return $this->algorithm;
    }
    
    protected function getSaltLength()
    {
        return $this->saltLength;
    }
    
    public static function validate($password)
    {
        if(strlen($password) > self::MAX_PASSWORD_LIMIT || strlen($password) < self::MIN_PASSWORD_LIMIT )
        {
            throw new PasswordException("密码格式不正确");
        }
        if(!preg_match("#^(?![A-Z]+$)(?![a-z]+$)(?!\d+$)\S{".self::MIN_PASSWORD_LIMIT.",".self::MAX_PASSWORD_LIMIT."}$#", $password))
        {
            throw new PasswordException("密码格式必须包含大小写字母");
        }
        return true;
    }
    
}
