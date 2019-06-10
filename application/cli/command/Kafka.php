<?php
/**
 * 服务器配置cli
 *
 * @author Dream<hukaijun@emicnet.com>
 */
namespace app\cli\command;

use think\console\Command;
use think\console\input\Argument;
use think\console\Input;
use think\console\Output;
use \RdKafka as RdKafka;
use think\exception\ThrowableError;

/**
 * Description of creat
 * php /var/pbx/website/think Config {$option} {$eid} {$key} {$value} {$describe} {$type}
 */
class Kafka extends Command
{
    //101.200.59.209 zk-0
    //39.106.25.77 zk-1
    //47.94.139.172 zk-2
    const ZK_LIST_LOCAL = "ZooKeeper-Kafka-01:9092,ZooKeeper-Kafka-02:9092,ZooKeeper-Kafka-03:9092";
    const ZK_LIST_EDV = "zk-1:9090,zk-1:9091,zk-1:9092";

    const TEST_TOPIC_NAME = "Alert";
    const MSG_PATH = 10;

    // 监控器
    private $monitor = null;

    protected function configure()
    {
        $this->setName('kafka')->setDescription('Kafka test~!');
        // 设置参数

        $this->addArgument('option', Argument::REQUIRED); // 参数 选项 producer
    }

    /**
     * 执行命令 php think kafka
     * @see \think\console\Command::execute()
     */
    public function execute(Input $input, Output $output)
    {
        $option  = $input->getArgument("option");
        if ($option == "producer") {
            $conf = new RdKafka\Conf();
            $conf->setDrMsgCb(function ($kafka, $message) {
                file_put_contents("./dr_cb.log", var_export($message, true) . PHP_EOL, FILE_APPEND);
            });
            $conf->setErrorCb(function ($kafka, $err, $reason) {
                file_put_contents("./err_cb.log", sprintf("Kafka error: %s (reason: %s)", rd_kafka_err2str($err), $reason) . PHP_EOL, FILE_APPEND);
            });
            $rk = new RdKafka\Producer($conf);
            $rk->setLogLevel(LOG_DEBUG);
            $rk->addBrokers(self::ZK_LIST_EDV);
            $cf = new RdKafka\TopicConf();
            // -1必须等所有brokers同步完成的确认 1当前服务器确认 0不确认，这里如果是0回调里的offset无返回，如果是1和-1会返回offset
            // 我们可以利用该机制做消息生产的确认，不过还不是100%，因为有可能会中途kafka服务器挂掉
            $cf->set('request.required.acks', "all");
            $topic = $rk->newTopic(self::TEST_TOPIC_NAME, $cf);

            $count = 0;
            while (true)
            {
                $content = "php_log:error:" . ++$count;
                $time = date("m-d H:i:s");
                $message = json_encode(array("time"=>$time,"content"=>$content));
                echo "Create message [$message]".PHP_EOL;
                $topic->produce(RD_KAFKA_PARTITION_UA, 0, $message);
                $len = $rk->getOutQLen();
                echo "sleep 10s ";
                for ($i=0;$i<10;$i++){
                    sleep(1);
                    echo ">";
                }
                if($len%self::MSG_PATH === 0){
                    echo "Send message!".PHP_EOL;
                    $rk->poll(self::MSG_PATH);
                }
            }
        }elseif ($option =="consumer1"){
            $this->consumer1();
        }
        elseif($option =="consumer"){
            $this->consumer();
        }elseif ($option =="hight_consumer")
        {
            $this->hight_consumer();
        }
        echo "success";
    }

    //高级消费
    protected function hight_consumer()
    {
        $conf = new RdKafka\Conf();
        // Set a rebalance callback to log partition assignments (optional)
        $conf->setRebalanceCb(function (RdKafka\KafkaConsumer $kafka, $err, array $partitions = null) {
            switch ($err) {
                case RD_KAFKA_RESP_ERR__ASSIGN_PARTITIONS:
                    echo "Assign: ";
                    var_dump($partitions);
                    $kafka->assign($partitions);
                    break;

                case RD_KAFKA_RESP_ERR__REVOKE_PARTITIONS:
                    echo "Revoke: ";
                    var_dump($partitions);
                    $kafka->assign(NULL);
                    break;

                default:
                    throw new \Exception($err);
            }
        });

// Configure the group.id. All consumer with the same group.id will consume
// different partitions.
        $conf->set('group.id', 'log_monitor');

// Initial list of Kafka brokers
        $conf->set('metadata.broker.list', self::ZK_LIST_EDV);

        $topicConf = new RdKafka\TopicConf();

// Set where to start consuming messages when there is no initial offset in
// offset store or the desired offset is out of range.
// 'smallest': start from the beginning
        $topicConf->set('auto.offset.reset', 'smallest');

// Set the configuration to use for subscribed/assigned topics
        $conf->setDefaultTopicConf($topicConf);

        $consumer = new RdKafka\KafkaConsumer($conf);

// Subscribe to topic 'test'
        $consumer->subscribe([self::TEST_TOPIC_NAME]);

        echo "Waiting for partition assignment... (make take some time when\n";
        echo "quickly re-joining the group after leaving it.)\n";

        while (true) {
            $message = $consumer->consume(120*1000);
            switch ($message->err) {
                case RD_KAFKA_RESP_ERR_NO_ERROR:
                    var_dump($message);
                    break;
                case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                    echo "No more messages; will wait for more\n";
                    break;
                case RD_KAFKA_RESP_ERR__TIMED_OUT:
                    echo "Timed out\n";
                    break;
                default:
                    throw new \Exception($message->errstr(), $message->err);
                    break;
            }
        }
    }



    //kafka LOW 级别消费
    protected function consumer()
    {
        $conf = new RdKafka\Conf();
        // Set a rebalance callback to log partition assignments (optional)
        // 当有新的消费进程加入或者退出消费组时，kafka 会自动重新分配分区给消费者进程，这里注册了一个回调函数，当分区被重新分配时触发
        $conf->setRebalanceCb(function (RdKafka\KafkaConsumer $kafka, $err, array $partitions = null) {
            switch ($err) {
                case RD_KAFKA_RESP_ERR__ASSIGN_PARTITIONS:
                    echo "Assign: ";
                    var_dump($partitions);
                    $kafka->assign($partitions);
                    break;

                case RD_KAFKA_RESP_ERR__REVOKE_PARTITIONS:
                    echo "Revoke: ";
                    var_dump($partitions);
                    $kafka->assign(NULL);
                    break;

                default:
                    throw new \Exception($err);
            }
        });

        // 配置groud.id 具有相同 group.id 的consumer 将会处理不同分区的消息，所以同一个组内的消费者数量如果订阅了一个topic， 那么消费者进程的数量多于 多于这个topic 分区的数量是没有意义的。
        $conf->set('group.id', 'myConsumerGroupPhp');

        //添加 kafka集群服务器地址
        $conf->set('metadata.broker.list', self::ZK_LIST_EDV);

        $topicConf = new RdKafka\TopicConf();


        // Set where to start consuming messages when there is no initial offset in
        // offset store or the desired offset is out of range.
        // 'smallest': start from the beginning
        //当没有初始偏移量时，从哪里开始读取
        $topicConf->set('auto.offset.reset', 'smallest');


        // Set the configuration to use for subscribed/assigned topics
        $conf->setDefaultTopicConf($topicConf);

        $consumer = new RdKafka\KafkaConsumer($conf);

        // 让消费者订阅log 主题
        $consumer->subscribe(['test']) ;

        while (true) {
            try{
                $message = $consumer->consume(12*1000);
            }catch (ThrowableError $e){
                echo $e->getMessage();
            }
            switch ($message->err) {
                case RD_KAFKA_RESP_ERR_NO_ERROR:
                    var_dump($message);
                    break;
                case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                    echo "No more messages; will wait for more\n";
                    break;
                case RD_KAFKA_RESP_ERR__TIMED_OUT:
                    echo "Timed out\n";
                    break;
                default:
                    throw new \Exception($message->errstr(), $message->err);

                    break;
            }

            echo "No more messages";
        }
    }


    protected function consumer1()
    {
        $conf = new RdKafka\Conf();
        $conf->setDrMsgCb(function ($kafka, $message) {
            file_put_contents("./c_dr_cb.log", var_export($message, true), FILE_APPEND);
        });
        $conf->setErrorCb(function ($kafka, $err, $reason) {
            file_put_contents("./err_cb.log", sprintf("Kafka error: %s (reason: %s)", rd_kafka_err2str($err), $reason).PHP_EOL, FILE_APPEND);
        });
        $conf->setRebalanceCb(function (RdKafka\KafkaConsumer $kafka, $err, array $partitions = null) {
            switch ($err) {
                case RD_KAFKA_RESP_ERR__ASSIGN_PARTITIONS:
                    echo "Assign: ";
                    var_dump($partitions);
                    $kafka->assign($partitions);
                    break;

                case RD_KAFKA_RESP_ERR__REVOKE_PARTITIONS:
                    echo "Revoke: ";
                    var_dump($partitions);
                    $kafka->assign(NULL);
                    break;

                default:
                    throw new \Exception($err);
            }
        });

        //设置消费组
        $conf->set('group.id', 'myConsumerGroup');
        $conf->set('client.id', 'php.system.kafka');
        //kafka集群
        $conf->set('metadata.broker.list', self::ZK_LIST_EDV);

        $rk = new RdKafka\Consumer($conf);
        $rk->addBrokers(self::ZK_LIST_EDV);

        $topicConf = new RdKafka\TopicConf();
        $topicConf->set('request.required.acks', 1);
//在interval.ms的时间内自动提交确认、建议不要启动
//$topicConf->set('auto.commit.enable', 1);
        $topicConf->set('auto.commit.enable', 0);
        $topicConf->set('auto.commit.interval.ms', 100);

// 设置offset的存储为file
//$topicConf->set('offset.store.method', 'file');
// 设置offset的存储为broker
        $topicConf->set('offset.store.method', 'broker');

//$topicConf->set('offset.store.path', __DIR__);

//smallest：简单理解为从头开始消费，其实等价于上面的 earliest
//largest：简单理解为从最新的开始消费，其实等价于上面的 latest
//$topicConf->set('auto.offset.reset', 'smallest');

        $topic = $rk->newTopic("Demo", $topicConf);

// 参数1消费分区0
// RD_KAFKA_OFFSET_BEGINNING 重头开始消费
// RD_KAFKA_OFFSET_STORED 最后一条消费的offset记录开始消费
// RD_KAFKA_OFFSET_END 最后一条消费
        $topic->consumeStart(0, RD_KAFKA_OFFSET_BEGINNING);
//$topic->consumeStart(0, RD_KAFKA_OFFSET_END); //
//$topic->consumeStart(0, RD_KAFKA_OFFSET_STORED);

        while (true) {
            //参数1表示消费分区，这里是分区0
            //参数2表示同步阻塞多久
            $message = $topic->consume(0, 12 * 1000);
            if (is_null($message)) {
                sleep(1);
                echo "No more messages\n";
                continue;
            }
            switch ($message->err) {
                case RD_KAFKA_RESP_ERR_NO_ERROR:
                    var_dump($message);
                    break;
                case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                    echo "No more messages; will wait for more\n";
                    break;
                case RD_KAFKA_RESP_ERR__TIMED_OUT:
                    echo "Timed out\n";
                    break;
                default:
                    throw new \Exception($message->errstr(), $message->err);
                    break;
            }
        }
    }
}