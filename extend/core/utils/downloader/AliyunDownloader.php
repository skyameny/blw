<?php
/**
 * 阿里云下载器
 * 修改阿里云出入参数
 * @author 
 * @from Talksystem 
 */
namespace moudles\core\utils\downloader;


use moudles\core\exception\CommonException;
use moudles\core\utils\ExLog;
use moudles\core\models;
use moudles\inspection\models\SystemConfig;
use moudles\core\utils\helper\helpersCurl;

class AliyunDownloader  implements  Downloader
{
    protected $client;
    protected $bucket;
    
    private $refresh_token ;
    private $client_secret ;
    
    public function __construct($authData = [])
    {
        $this->refresh_token = $authData['refresh_token'];
        $this->client_secret = $authData['client_secret'];
    }
    
    /**
     * 文件下载
     *  192.168.1.214/talk/test/download
     * @param array $array
     */
    public function download($authData= [])
    {
        $eid = $_GET['eid'];
        $serverParam["client_id"] = empty($authData['client_id'])? '':$authData['client_id'];
        $serverParam["local_server_ip"] = empty($authData['local_server_ip'])? '':$authData['local_server_ip'];
        $serverParam["redirect_uri"]  = empty($authData['redirect_uri'])? '':$authData['redirect_uri'];
        $serverParam["oauth_server_ip"] = empty($authData['oauth_server_ip'])? '':$authData['oauth_server_ip'];
        $serverParam["oauth_server_outip"] = empty($authData['oauth_server_outip'])? '':$authData['oauth_server_outip'];
        $serverParam["refresh_token"] = $this->refresh_token;
        $serverParam["client_secret"] = $this->client_secret;
        $serverParam["file_server_outip"] = empty($authData['file_server_outip'])? '':$authData['file_server_outip'];
        $_SESSION['request_authorize_state'] = time();
        
        $authStr = sprintf("%s:%s",$serverParam['client_id'],$serverParam['client_secret']);
        $user_id = sprintf("%s_%s",$serverParam['client_id'],$eid);
        $setOpt = array(
            "CURLOPT_HTTPAUTH"=>CURLAUTH_BASIC,
            "CURLOPT_USERPWD" =>$authStr,
            "port"=>"1058"
        );
        $header = array(
            "PHP_AUTH_USER" =>$serverParam['client_id'],
            "PHP_AUTH_PW" =>$serverParam['client_secret']
        );
        $e_time = 525600;
//         $e_time = EnterpriseModel::GetProfileByKey($eid, 'url_expires_time');
        if(empty($e_time)){
            $e_time = 120*60;
        }else{
            if(is_numeric($e_time)){
                $e_time = $e_time*60;
            }else{
                ExLog::log(__METHOD__.': get key=url_expires_time fail,eid='.$eid,ExLog::DEBUG);
                throw  new  CommonException('下载失败');
                //return array('data'=>'', 'info'=>'1037-链接时效参数错误', 'status'=>11);
            }
        }
        
        $postDatas = array(
            "user_id"=>$user_id,
            "grant_type"=>"refresh_code",
            "refresh_token"=>$serverParam['refresh_token'],
            "redirect_uri"=>$serverParam['redirect_uri'],
            "etime" => $e_time
        );
        $url = sprintf("https://%s:1058/Oauth/Api/refresh_token_ssl",$serverParam['oauth_server_ip']);
        $curl = new helpersCurl($setOpt,$header);
        $jsonData = $curl->post($url,$postDatas);
        $data = json_decode($jsonData,true);
        if(empty($data) || $data['status'] > 0){
            ExLog::log(__METHOD__.': refresh token fail',ExLog::DEBUG);
            throw  new  CommonException('1043-未获得授权访问网盘服务器');
        }
        $atData = $data['data'];
        //$resToken 通过 $crData = CallRecordModel::FindCallrecordByCcnumber($eid, $ccNumber)得到
        $resToken = 'f6f7727999006e30f4f00172dda1b8cb1528102494bpxFceQg';
        // $url_type = EnterpriseModel::GetProfileByKey($eid, 'record_download_url_type');
        $url_type=1;
        if(empty($url_type)){
            $data['url'] = sprintf("https://%s:1056/User/Api/getFile?access_token=%s&res_token=%s",$serverParam['file_server_outip'],$atData['access_token'],$resToken);
        }else{
            $data['url'] = sprintf("http://%s:1055/User/Api/getFile?access_token=%s&res_token=%s",$serverParam['file_server_outip'],$atData['access_token'],$resToken);
        }
        $furl = sprintf("https://%s:1056/User/Api/getFileInfo",$serverParam['file_server_outip']);
        $postDatas2 = array(
            "access_token" => $atData['access_token'],
            "res_token" => $resToken
        );
        
        $curl2 = new helpersCurl(array("port"=>"1056"),array());
        $request = $curl2->post($furl,$postDatas2);
        $res = json_decode($request,true);
        if($res['status']==0){
            $data['size'] = $res['data']['size'];
        }
        $ss=  array('data'=>$data, 'info'=>time(), 'status'=>0);
        print_r($ss);exit('===');
    }
    
    /**
     * 文件上传
     * @param array $array
     */
    public function upload($array = [])
    {
        ;   
    }
    
    /**
     * 批量下载器
     * @param array $array
     */
    public function batchDownload($array = [])
    {
        
        ;
    }
    
    
    
    
    
    
    
}