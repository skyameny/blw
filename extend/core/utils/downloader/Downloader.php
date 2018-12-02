<?php
/**
 * 下载器
 * @author 
 * @from Talksystem 
 */
namespace moudles\core\utils\downloader;

interface Downloader
{
    /**
     * 文件下载
     * @param array $array
     */
    public function download($array = []); 
    
    /**
     * 文件上传
     * @param array $array
     */
    public function upload($array = []);
    
    /**
     * 批量下载器
     * @param array $array
     */
    public function batchDownload($array = []);
    
    
}