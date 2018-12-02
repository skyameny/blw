<?php
namespace core\model;

use think\Model;
use moudles\inspection\models\Enterprise;

/**
 * 动态数据库分库
 *
 * @author DONG Shengdong
 *
 */
abstract class DynamicModel extends Model
{
    public static function getEnterpriseId()
    {
        return defined("ENTERPRISE_IDENTITY")?ENTERPRISE_IDENTITY:null;
    }
    
    public function getOperator()
    {
        $enterprise = null;
        $eid = $this->getOperatorId();
        if(!empty($eid)){
            $enterprise = Operator::get($eid);
        }
        return $enterprise;
    }
    
    
    /**
     * 构造方法
     * @access public
     * @param array|object $data 数据
     */
    public function __construct($data=[])
    {
        $this->subTreasury();
        parent::__construct($data);
    }
    
    /**
     * 数据库分库
     * @access protected
     */
    private function subTreasury()
    {
        if (defined("DB_SUB_TREASURY") && DB_SUB_TREASURY) {
            $this->connection["database"] = self::getTreasury(DB_SUB_TREASURY);
        }
    }
 
    /**
     * 获取数据库名称
     *
     * @param string $treasury
     * @return string 数据库名
     */
    public static function getTreasury($treasury)
    {
        return DB_SUB_TREASURY_PREFIX . '_' . $treasury;
    }

    public function __destruct()
    {}
}