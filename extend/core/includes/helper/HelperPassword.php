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
    const MIN_PASSWORD_LIMIT = 8;

    private static $forbidden_keywords = array("root","admin","oracle","system","mysql");
    private $algorithm;
    private $saltLength;
    private static $allow_special_characters = '~!@#$%^&*()[]{}|:"<>?,./;\\\_-+’‘？、。，；：“”」「【】·`《》￥…';
    private static $str_continuities = array(
        "1234567890 0987654321",
        "qwertyuiop asdfghjkl zxcvbnm",
        "QWERTYUIOP ASDFGHJKL ZXCVBNM",
        "qaz wsx edc rfv tgb yhn ujm",
        "QAZ WSX EDC RFV TGB YHN UJM"
    );


    public function __construct($algorithm, $saltLength) {
        $this->algorithm = $algorithm;
        $this->saltLength = $saltLength;
    }

    /**
     * 加密
     * @param $password
     *
     * @return string
     * @throws PasswordException
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

    /**
     * @param $password
     * @throws PasswordException
     */
    public static function validate($password, $username = "")
    {
        if (mb_strlen($password) > self::MAX_PASSWORD_LIMIT || mb_strlen($password) < self::MIN_PASSWORD_LIMIT) {
            throw new PasswordException("密码长度必须是8到20位");
        }
        $char_i = $char_a = $char_A = $char_t = array();
        $last_char = "";
        $list_char_3 = "";//连续三个字符
        $chars =preg_split('/(?<!^)(?!$)/u', $password ); //也行是中文标点
        foreach ($chars as $char) {
            if ($last_char === $char) {
                throw new PasswordException("密码不能含有两个连续相同的字符[$char]");
            }
            $list_char_3 .= $char;
            $last_char = $char;
            //判断三连
            if (strlen($list_char_3) >= 3) {
                $list_char_3 = substr($list_char_3, strlen($list_char_3) - 3, 3);
                foreach (self::$str_continuities as $str_continuity) {
                    if (strpos($str_continuity, $list_char_3) !== false) {
                        throw new PasswordException("密码不能包括连续的3个字符键盘键位");
                    }
                }
            }
            if (is_numeric($char)) {
                $char_i[] = $char;
                continue;
            }
            $str = ord($char);
            if ($str > 64 && $str < 91) {
                //大写字母
                $char_A[] = $char;
                continue;
            }
            if ($str > 96 && $str < 123) {
                //小写字母
                $char_a[] = $char;
                continue;
            }
            //这里的特殊字符指
            if(strpos(self::$allow_special_characters,$char) !== false){
                $char_t[] = $char;
                continue;
            }
            //其他一切字符
            throw new PasswordException("密码含有系统不允许的特殊字符");
        }
        if (empty($char_i) || empty($char_a) || empty($char_A) || empty($char_t)) {
            throw new PasswordException("密码必须同时含有大写、小写、数字和特殊字符");
        }
        //关键字
        $forbidden_key = self::$forbidden_keywords;
        if(!empty($username)) {
            $forbidden_key = array_merge(self::$forbidden_keywords, (array)strtolower($username));
        }
        foreach ($forbidden_key as $keyword) {
            if (strpos(strtolower($password), $keyword) !== false) {
                throw new PasswordException("密码包含系统禁止的词汇或用户名");
            }
        }
        return true;
    }
}


