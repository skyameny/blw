<?php
/**
 * 金山下载器
 * @author Dream<hukaijun@emicnet.com>
 * @from Talksystem 
 */
namespace moudles\core\utils\downloader;


use moudles\core\exception\CommonException;
use moudles\core\utils\ExLog;

class KsDownloader  implements  Downloader
{
    protected $client;
    protected $bucket;
    
    private $accessKey;
    private $secretKey;
    private $endpoint;
    
    public function __construct($authData = [])
    {
        define("KS3_API_VHOST",FALSE);
        
        //是否开启日志(写入日志文件)
        
        define("KS3_API_LOG",TRUE);
        
        //是否显示日志(直接输出日志)
        
        define("KS3_API_DISPLAY_LOG", false);
        
        //定义日志目录(默认是该项目log下)
        
        define("KS3_API_LOG_PATH",QIS_LOG_PATH."ks".DS);
        
        //是否使用HTTPS
        
        define("KS3_API_USE_HTTPS",FALSE);
        
        //是否开启curl debug模式
        
        define("KS3_API_DEBUG_MODE",FALSE);
        
        require_once  VENDOR_PATH . '/ks3/Ks3Client.class.php';
        
        $this->bucket    = $authData['bucket'];
        $this->accessKey = $authData['accessKey'];
        $this->secretKey = $authData['secretKey'];
        $this->endpoint  = $authData['endpoint'];
        
        $this->client = new \Ks3Client($this->accessKey, $this->secretKey, $this->endpoint);
    }
    
    /**
     * 文件下载
     *
     * @param array $array
     */
    public function download($array = [])
    {
        if(empty($array['key']) || empty($array['writeTo'])){
            return  false;
        }
      
        $args = array(
            "Bucket" =>$this->bucket,
            "Key"    =>$array['key'], //文件名
            "WriteTo"=>$array['writeTo'],//下载到路径
        );
        $result = $this->client->getObject($args);
        
        if($result == false){
            ExLog::log('下载失败！'.json_encode($result));
            throw  new  CommonException('下载失败');
        }
        return  true;
    }
    
    /**
     * 文件上传
     * @param array $array
     */
    public function upload($array = [])
    {
        ;   
    }
    
    public  function  batchDownload($array = []){
        //do nothing;
        ;
    }
    
    
    
    
    
}