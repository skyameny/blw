<?php
/**
 * 数据导出
 * User: Dream<1015617245@qq.com>
 * Date: 2019-03-21
 * Time: 20:26
 */
namespace core\utils\export;

interface  export
{
    /**
     * @param string $title
     * @return string
     */
    public function setTitle($title="");

    /**
     * @param array $header
     * @return array
     */
    public function  setHeader($header = []);

    /**
     * 设置文件名称
     * @param  string $filename
     * @return string boolean
     */
    public function setFileName($filename="");

    /**
     * 导出运行方法
     * @return mixed
     */
    public function process();


    /**
     * 设置导出值
     * @param array $params
     * @return array()
     */
    public function setData($params=[]);

    /**
     * 设置百分比
     * @param int $percent
     * @return mixed
     */
    public function setPercent($percent = 0);

    /**
     * 获取百分比
     * @return int
     */
    public function getPercent();

    /**
     * 获取文件
     * @return mixed
     */
    public function getFile();

}