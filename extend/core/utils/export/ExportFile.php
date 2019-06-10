<?php
/**
 * 导出文件格式
 * User: Dream<1015617245@qq.com>
 * Date: 2019-03-21
 * Time: 20:26
 */
namespace core\utils\export;

interface ExportFile
{
    /**
     * 设置文件名称
     * @param string $filename
     * @return mixed
     */
    public function setFileName($filename);


    public function write($data);
}

