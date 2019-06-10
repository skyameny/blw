<?php
/**
 * 文本文件
 * User: keepwin100
 * Date: 2019-03-22
 * Time: 16:18
 */

namespace core\utils\export\impl;

use core\includes\helper\HelperDirectory;
use core\utils\export\ExportFile;

abstract class TextExportFile implements ExportFile
{
    protected $inCharset = 'UTF-8';
    protected $outCharset = 'GB18030//TRANSLIT';

    protected $filename = "";

    /**
     * 设置文件目录和文件名称
     * @param string $filename
     * @return bool|mixed
     */
    public function setFileName($filename)
    {
        $this->filename = $filename;
      // $this->filename = self::iconv($filename,"utf-8","gb2312");
       $path = dirname($this->filename);
       if(!is_dir($path)){
           $result = HelperDirectory::directory($path);
           if(!$result){
               return false;
           }
       }
       return true;
    }

    /**
     * @param mixed $data
     * @return mixed
     */
    abstract public function write($data);

    /**
     * 转码
     * @param $value
     * @param string $in_charset
     * @param string $out_charset
     * @return false|string
     */
    protected  function iconv($value,$in_charset = "",$out_charset="")
    {
        if(empty($in_charset)){
            $in_charset = $this->inCharset;
        }
        if(empty($out_charset)){
            $out_charset = $this->outCharset;
        }

        return iconv($in_charset,$out_charset, $value);
    }
}