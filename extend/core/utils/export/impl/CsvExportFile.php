<?php
/**
 * csv文件导出
 * User: Dream<hukaijun@emicnet.com>
 * Date: 2019-03-22
 * Time: 11:15
 */

namespace core\utils\export\impl;

class CsvExportFile extends TextExportFile
{

    /**
     * 写入数据
     * @param mixed $data
     * @return mixed|void
     */
    public function write($data)
    {
        $line = "";
        if(is_array($data)){
            $line = implode(",",$data);
            $line = self::iconv($line);
        }
        if(is_string($data)){
            $line = self::iconv($data);
        }
        if(is_object($data)){
            $line = self::iconv($data->toString());
        }
        file_put_contents($this->filename,$line.PHP_EOL,FILE_APPEND);
    }

}
