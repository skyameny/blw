<?php
/**
 * 队列
 */
namespace moudles\core\utils;
use think\cache\driver\Redis as DrRedis;
use think\Config;

class Queue extends DrRedis
{
    static public $timeout = 1;
    static public $queueName = 'queue';
    /**
     * 操作句柄
     * @var string
     * @access protected
     */
    protected $handler;
    /**
     * 缓存连接参数
     * @var integer
     * @access protected
     */
    protected $options = array();
    /**
     * 取得缓存类实例
     * @static
     * @access public
     * @return mixed
     */
    public static function getInstance($queueName, $options = [])
    {
        if (Config::get('cache.type') != 'Redis') exit('DATA_CACHE_TYPE DO NOT Support Redis');
        //当前队列名称
        self::$queueName = $queueName;
        static $_instance = array();
        if (!isset($_instance[$queueName])) {
            $_instance[$queueName] = new Queue(config("redis"));
        }
        return $_instance[$queueName];
    }
    //设置队列名称
    public static function setQueueName($name)
    {
        self::$queueName = $name;
    }
    /**
     * 添加队列(lpush)
     * @param string $value
     * @return int 队列长度
     * @return Bool 是否从右
     */
    public function push($key, $value ,$right = true) {
        $value = json_encode($value);
        return $right ? $this->handler->rPush($key, $value) : $this->handler->lPush($key, $value);  
    }
    //brpop
    /** 
     * 数据出队列 
     * @param string $key KEY名称 
     * @param bool $left 是否从左边开始出数据 
     */  
    public function pop($key , $left = true) {  
        $key = self::$queueName;
        $val = $left ? $this->handler->lPop($key) : $this->handler->rPop($key);  
        return json_decode($val,true);  
    }
    
    /**
     * 删除一条数据
     * @param string $key KEY名称
     */
    public function delete($key) {
        return $this->handler->delete($key);
    }
    
    /**
     * 删除一个消息队列
     */
    public function flushQueue()
    {
        $this->delete(self::$queueName);
    }
    /**
     * 返回队列长茺
     * @return int
     */
    public function len()
    {
        return $this->handler->lSize(self::$queueName);
    }
}
?>