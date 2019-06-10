<?php
namespace authority\includes\helper;


use authority\exception\PasswordException;
use core\includes\helper\HelperRandom;
use core\utils\ExLog;

/**
 * Password Hash class.
 *
 * An helper class focusing on password validation/generation.
 */
class HelperPassword
{
    const MAX_PASSWORD_LIMIT = 20;
    const MIN_PASSWORD_LIMIT = 8;

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
     * @throws
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
        ExLog::log("hash:".$hashed."===".hash($this->getAlgorithm(), $salt.$password));
        return hash($this->getAlgorithm(), $salt.$password) === $hashed;
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
            throw new PasswordException();
        }
        if(!preg_match("#^(?![A-Z]+$)(?![a-z]+$)(?!\d+$)\S{".self::MIN_PASSWORD_LIMIT.",".self::MAX_PASSWORD_LIMIT."}$#", $password))
        {
            throw new PasswordException();
        }
        return true;
    }

}
