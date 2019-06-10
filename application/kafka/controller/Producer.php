<?php
/**
 * Created by PhpStorm.
 * User: keepwin100
 * Date: 2019-01-02
 * Time: 10:45
 */
namespace app\kafka\controller;

use core\controller\Base;
use \RdKafka as RdKafka;

class Producer extends Base
{
    protected $no_auth_action=[];

    public function index()
    {
        $conf = new RdKafka\Conf();
        $conf->setDrMsgCb(function ($kafka, $message) {
            file_put_contents("./dr_cb.log", var_export($message, true).PHP_EOL, FILE_APPEND);
        });
        $conf->setErrorCb(function ($kafka, $err, $reason) {
            file_put_contents("./err_cb.log", sprintf("Kafka error: %s (reason: %s)", rd_kafka_err2str($err), $reason).PHP_EOL, FILE_APPEND);
        });

        $rk = new RdKafka\Producer($conf);
        $rk->setLogLevel(LOG_DEBUG);
        $rk->addBrokers("127.0.0.1");

        $cf = new RdKafka\TopicConf();
// -1必须等所有brokers同步完成的确认 1当前服务器确认 0不确认，这里如果是0回调里的offset无返回，如果是1和-1会返回offset
// 我们可以利用该机制做消息生产的确认，不过还不是100%，因为有可能会中途kafka服务器挂掉
        $cf->set('request.required.acks', 0);
        $topic = $rk->newTopic("test", $cf);

        $option = 'qkl';
        for ($i = 0; $i < 20; $i++) {
            //RD_KAFKA_PARTITION_UA自动选择分区
            //$option可选
            $topic->produce(RD_KAFKA_PARTITION_UA, 0, "qkl . $i", $option);
        }


        $len = $rk->getOutQLen();
        while ($len > 0) {
            $len = $rk->getOutQLen();
            var_dump($len);
            $rk->poll(50);
        }

        echo "success";
    }



}