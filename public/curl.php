<?php
echo "ts";
function postRequest($url, $data, $refer = "", $timeout = 50000, $header = array())
{
    $curlObj = curl_init();
    $ssl = stripos($url,'https://') === 0 ? true : false;
    $options = array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_FOLLOWLOCATION => 1,
        CURLOPT_AUTOREFERER => 1,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)',
        CURLOPT_TIMEOUT => $timeout,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_0,
        CURLOPT_HTTPHEADER => array('Expect:'),
        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
        CURLOPT_REFERER => $refer
    );
    if (!empty($header)) {
        $options[CURLOPT_HTTPHEADER] = $header;
    }
    if ($refer) {
        $options[CURLOPT_REFERER] = $refer;
    }
    if ($ssl) {
        //support https
        $options[CURLOPT_SSL_VERIFYHOST] = false;
        $options[CURLOPT_SSL_VERIFYPEER] = false;
    } else {
        curl_setopt($curlObj, CURLOPT_SSLVERSION,3);
    }
    curl_setopt_array($curlObj, $options);
    $returnData = curl_exec($curlObj);
    var_dump($returnData);
    if (curl_errno($curlObj)) {
        //error message
        $returnData = curl_error($curlObj);
    }
    curl_close($curlObj);
    return $returnData;
}

$data['account_ids'] = [1];
$send = json_encode($data);
$url = "http://101.200.59.209:9009/AI/sysBackstage/getAccountStatus";

function post($url, $data) {
    $data = http_build_query($data);

    $opts = array (
        'http' => array (
            'method' => 'POST',
            'header'=> "Content-type: application/json",
            "Content-Length: " . strlen($data) . "rn",
            'content' => $data
        )
    );

    $context = stream_context_create($opts);
    $html = file_get_contents($url, false, $context);
    return $html;
}
var_dump($send);
var_dump(file_get_contents($url));
var_dump(post($url, $send));
$header = array("Content-type: application/json");
var_dump(postRequest($url, $send, 10000, $header));
die;