<?php
/**
 * 社区模型
 */

namespace community\model;

use core\model\BlModel;

class Community extends BlModel
{
    //社区运营状态
    const TYPE_OPTION_WUYE = 0;
    //居委会管理
    const TYPE_OPTION_JUWEI = 1;
    //默认状态 无运营商管理
    const TYPE_OPTION_DEFAULT = 2; 
    //社区注册状态
    const STATUS_REGISTER = 1;
    //未注册
    const STATUS_UNREGISTER = 2;
    
    
    protected $likeColumn = ["address","name"];
    
    protected function profile()
    {
        return $this->hasOne("CommunityProfile","cid","id");
    }
    
    /**
     * 获取所在地址
     *
     */
    public function getAddress()
    {
        
    }
    
    public function getBussessNum()
    {
        return 103;
    }
    
    public function getBanner()
    {
        return 'dist/img/photo1.png';
    }

    /**
     * 获取管理运营商
     */
    public function getOperator()
    {
        $operator = null;
        if ($this->isOperatored()) {
            $operator = Operator::get($this->getAttr("op_id"));
        }
        return $operator;
    }
    
    /**
     * 获取社区信息
     */
    public function getInfo($type="base")
    {
        switch ($type) {
            case "base":
                ;
                break;
                
            default:
                ;
                break;
        }
    }
    
    /**
     * 是否被接盘管理
     * 
     */
    public function isOperatored()
    {
        $op_id = $this->getAttr("op_id");
        return !empty($op_id);
    }
    
    
    /**
     * 绑定管理运营商
     */
    public function bindOperator(Operator $operator)
    {
        
    }
}