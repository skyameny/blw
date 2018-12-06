<?php
/**
 * 服务管理器 
 * @author keepwin100
 * @package emicall
 * @copyright 苏ICP备08006818号
 */
namespace core\service;



use core\exception\ServiceException;
use think\Config;
use think\Loader;


class ServiceManagement
{
    private static $instance;

    public static function singleton()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private static $services = array();

    private $configService;

    private function __construct()
    {
        //do nothing
    }

    /**
     * get service by serviceKey
     *
     * @param string $serviceKey
     * @throws 
     * @throws ServiceException
     */
    public function get($serviceKey)
    {
        $service = null;
        
        if (!$this->has($serviceKey)){
            $serviceClass = $serviceKey;
            if(strpos($serviceClass, "\\") === false){
                $modules = Config::get("bl_extend_modules");
                foreach ($modules as $module)
                {
                    $serviceClass = $module."\\service\\".Loader::parseName($serviceKey,1)."Service";
                    if(class_exists($serviceClass)){
                        break;
                    }
                    $serviceClass = "";
                }
            }
            if(class_exists($serviceClass)){
                $service = $serviceClass::singleton();
            }

            if ($service === false) {
                throw new ServiceException($serviceKey,100000);
            }
            
            if (!$service instanceof Service) {
                throw new ServiceException($serviceKey.' service must instance of Service');
            }
            //self::$services[$serviceKey] = $service;
            $this->register($serviceKey, $service);
        }
        return self::$services[$serviceKey];
    }

    /**
     * (non-PHPdoc)
     * @see 
     */
    public function has($serviceKey)
    {
        return isset(self::$services[$serviceKey]);
    }

    /**
     * 
     */
    protected function register($serviceKey, Service $service)
    {
        self::$services[$serviceKey] = $service;
    }
}