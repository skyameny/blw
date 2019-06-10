<?php
/**
 * 【BL服务器】 Service服务类
 * @author Dream <hukaijun@emicnet.com>
 * @package emicall
 * @copyright 苏ICP备08006818号
 */
namespace core\service;

use core\exception\CommonException;

abstract class Service {
    // --- ASSOCIATIONS ---
    // --- ATTRIBUTES ---
    
    /**
     *
     * @access private
     * @var array
     */
    private static $instances = array();
    
    /**
     * service 名称标准
     *
     * @access private
     * @var string
     */
    
    const namePattern = 'bl_%1$sService';
    
    // --- OPERATIONS ---
    
    /**
     *
     * @access protected
     * @return mixed
     */
    protected function __construct() {

    }

    
    /**
     *
     * @access public
     * @deprecated
     *
     * @param  string $serviceName
     *
     * @return
     * @throws
     */
    public static function getServiceByName($serviceName) {
        $returnValue = null;
        
        $className = (!class_exists($serviceName) || !preg_match("/^(core|identify)/", $serviceName)) ? sprintf(self::namePattern, ucfirst(strtolower($serviceName))) : $serviceName;
        
        if (!class_exists($className)) {
            throw new CommonException('Tried to init abstract class ' . $className);
        }
        $class = new \ReflectionClass($className);
        if ($class->isAbstract()) {
            throw new commonException('Tried to init abstract class ' . $className . ' for param \'' . $serviceName . '\'');
        }
        if (!$class->isSubclassOf('Service')) {
            throw new commonException("$className must refer to a class extending the Service");
        }
        if (!isset(self::$instances[$className])) {
            self::$instances[$className] = new $className();
        }
        
        $returnValue = self::$instances[$className];
        
        
        return $returnValue;
    }
    
    /**
     * 单例
     * @access public
     * @author Dream<hukaijun@emicnet.com>
     * @return mixed
     */
    public static function singleton() {
        $returnValue = null;
        
        $serviceName = get_called_class();
        if (!isset(self::$instances[$serviceName])) {
            self::$instances[$serviceName] = new $serviceName();
        }
        
        $returnValue = self::$instances[$serviceName];
        
        return $returnValue;
    }
    
}