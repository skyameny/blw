<?php
/**
 * 数据导出
 * User: Dream<1015617245@qq.com>
 * Date: 2019-03-21
 * Time: 20:26
 */

namespace core\utils\export;

use core\exception\CoreException;
use core\utils\CliExcutor;
use core\utils\ExLog;
use core\utils\export\exception\ExportException;
use core\utils\export\impl\CsvExportFile;
use core\utils\export\impl\TextExportFile;
use think\Cache;

abstract class EmExport implements export
{
    const STATUS_READY = 0;
    const STATUS_RUNNING = 1;
    const STATUS_FINISH = 2;
    #目前仅仅支持csv
    const FILE_TYPE_SUFFIX = ".csv";

    protected $command = "php %s/think export:common -p %s -f %s";

    protected $percent = 0;

    protected $filename = "";

    protected $out_filename = "";

    protected $hash = "";

    protected $header = [];

    protected $title = "";

    protected $footer = "统计由易米云计算提供";

    /**
     * @var TextExportFile
     */
    protected $handler = null;
    # 状态
    protected $status = 0;

    #导出参数列表
    protected $param = array();
    //总行数
    protected $total_count = 0;
    //已经写入行数
    protected $current_count = 0;
    //数据流
    protected $stream_data = false;
    //开始时间
    protected $start_time = 0;
    //结束时间
    protected $end_time = 0;

    protected $cache = null;

    /**
     * 导出调度
     * @var unknown
     */
    protected $callable = null;

    public static function error($code)
    {
        $error_list = array(
            "10001" => "系统配置有误，无法导出",
            "10002" => "系统错误",
            "10003" => "数据为空",
            "10004" => "下载失败"
        );
        return $error_list[$code];
    }

    protected static function cache($key, $value = null)
    {
        if (empty($key)) {
            return null;
        }
        if (is_null($value)) {
            $result = Cache::get($key);
        } else {
            //缓存3600s
            $result = Cache::set($key, $value, 3600);
        }
        return $result;
    }

    /**
     * 实例化方法
     * @param string $hash
     * @param bool $sync
     * @param array $param
     * @param null $cmd
     * @param null $callable
     * @param string $action
     * @return bool|EmExport|mixed|null
     */
    public static function getInstance($hash = "", $param = array(), $sync = false, $cmd = null, $callable = null, $action = 'SaveDate')
    {

        $instance = self::cache($hash);
        if (empty($instance)) {
            try {
                $class = get_called_class();
                if(get_called_class() == get_class()){
                    return null;
                }
//                $object = new \ReflectionClass($class);
//                if($object->isAbstract()) {
//                    return null;
//                }
                return new $class($hash, $param);
            } catch (ExportException $e) {
                $instance = null;
            }
        }
        return $instance;
    }

    /**
     * 核心方法
     * 写入数据
     * @return mixed|void
     * @throws ExportException
     */
    public function process()
    {
        ExLog::log("开始导入数据", ExLog::DEBUG);
        $this->start_time = microtime(true);
        $this->status = self::STATUS_RUNNING;
        if (empty($this->filename)) {
            $fileName = (string)$this->getPath() . "RPT_" . $this->hash . self::FILE_TYPE_SUFFIX;
            $this->filename = $fileName;
        }
        if (empty($this->header)) {
            $this->header = $this->setDefaultHeader();
        }
        if (empty($this->title)) {
            $this->title = $this->setDefaultTitle();
        }

        #目前这里只能导出csv 有空再细化
        $this->handler = new CsvExportFile();

        $this->handler->setFileName($this->filename);
        //写入表头
        $this->handler->write($this->title);
        $this->handler->write($this->header);
        //写入正文 这里先支持简单的数据 一次性获取的
        if ($this->stream_data) {
//            $this->total_count = $this->getTotalCount();

//            $data = $this->setData();
//            while($data !== ""){
//                foreach ($this->setData() as $data)
//                {
//                    $this->handler->write($data);
//                }
//            }
            exit("暂不支持数据流");
        }
        $param = $this->param;
        $datas = $this->setData($param);
        if (!is_array($datas)) {
            ExLog::log(get_called_class() . "getData数据格式与预导出格式不合");
        }
        foreach ($datas as $data) {
            //长度不一致需要报错
            if ((count($data) != count($this->header))) {
                throw new ExportException("输入的数据与格式不符", PARAM_ERROR);
            }
            $this->handler->write($data);
        }
        $this->endProcess();
    }

    protected function endProcess()
    {
        $this->end_time = microtime(true);
        if (!empty($this->footer)) {
            $this->setFooter();
        }
        $this->setPercent(100);
        $this->status = self::STATUS_FINISH;
    }

    /**
     * 设置导出
     */
    protected function setFooter()
    {
        //输入两个空行
        $this->handler->write(PHP_EOL);
        $time = floatval($this->end_time) - floatval($this->start_time);
        $m_time = (float)sprintf('%.0f', floatval($time) * 1000);
        $end_line = "统计完成：耗时$m_time(毫秒)。";
        $this->handler->write($end_line);
        $this->handler->write($this->footer);
    }

    /**
     * 创建唯一ID
     * @return string
     */
    public static function buildHash()
    {
        return "EXP_" . session_create_id();
    }

    /**
     * 获取Hash 列表
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * 构造函数 无法再外部构建
     * @param $hash
     * @param array $param
     * @throws ExportException
     */
    public function __construct($hash, $param = array())
    {
        if (empty($hash) && empty($param)) {
            ExLog::log("参数不合法，无法创建对象",ExLog::DEBUG);
            throw new ExportException("导入参数错误", PARAM_ERROR);
        }
        $this->hash = $hash;
        $this->param = $param;
        $this->param["class_name"] = get_called_class();
        $this->out_filename = $this->setDefaultFileName().self::FILE_TYPE_SUFFIX;
    }

    /**
     * 获取设置的文件名称
     * @return string
     */
    public function getOutFileName(){
        return $this->out_filename;
    }
    /**
     * 执行运行导出开始
     * 只会在导出开始是执行 无法被第二次执行
     * @throws ExportException
     */
    public function run()
    {
        if ($this->status != self::STATUS_READY) {
            throw new ExportException("该任务已经在运行了！", ILLEGAL_OPERATION);
        }
        if (empty($this->hash)) {
            $this->hash = self::buildHash();
            $this->status = self::STATUS_RUNNING;
        }
        $params = json_encode($this->param);
        $command = sprintf($this->command, ROOT_PATH, "'" . $params . "'", $this->hash);
        ExLog::log('正在执行导出命令：' . $command);
        CliExcutor::backExcuteCmd($command);
        $this->sync();
    }

    /**
     *清除当前对象数据
     */
    public function clear()
    {
        $this->handler = null;
        @unlink($this->filename);
        self::cache($this->hash, null);
    }

    /**
     *文件临时保存地址
     * @return string
     */
    public function getPath()
    {
        $eid = $this->param["eid"];
        if ($eid == null) {
            $path = AICALL_TMP_PATH . 'report/';
        } else {
            $path = AICALL_TMP_PATH . 'report/' . $eid . '/';
        }
        return $path;
    }

    /**
     * 前后台同步
     */
    public function sync()
    {
        self::cache($this->hash, $this);
    }

    public function getFile()
    {
        return $this->filename;
    }

    public function getPercent()
    {
        return intval($this->percent);
    }

    /**
     * 写入进度
     * @param int $percent
     * @return bool|mixed
     */
    public function setPercent($percent = 0)
    {
        if ($percent > 100 || $percent < 0) {
            return false;
        }
        $this->percent = $percent;
        $this->sync();
    }

    public function setHeader($header = [])
    {
        $this->header = $header;
    }

    public function setTitle($title = "")
    {
        $this->title = $title;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setFileName($filename = "")
    {
        if (substr($filename, -4, 4) === self::FILE_TYPE_SUFFIX) {
            $this->filename = $this->getPath() . $filename;
        } else {
            $this->filename = $this->getPath() . $filename . self::FILE_TYPE_SUFFIX;
        }
    }

    /**
     * @param $config
     * @return bool
     */
    protected function setConfig($config)
    {
        if (empty($config)) {
            return false;
        }
        foreach ($config as $_key => $_value) {
            if (in_array($_key, ["footer"])) {
                $this->$_key = $_value;
            }
        }
    }

    public function getParam($key)
    {
        if(isset($this->param[$key]))
        {
            return $this->param[$key];
        }else{
            return "";
        }
    }

    /**
     * 设置导出的标题
     * @return string
     */
    abstract protected function setDefaultTitle();

    /**
     * 设置导出的列表头
     * @return array
     */
    abstract protected function setDefaultHeader();

    /**
     * @return string
     */
    abstract protected function setDefaultFileName();

    /**
     * 设置数据
     * @param array $params
     * @return array
     */
    abstract public function setData($params = []);

    abstract public function getTotalCount();

}