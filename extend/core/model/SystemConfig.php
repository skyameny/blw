<?php 
/**
* 对象存储配置模型
* @date: 2018年8月17日 
* @update: Dream
*/
namespace core\model;

use think\Model;
use core\exception\CommonException;

class SystemConfig extends BlModel
{
    const SYSTEM_TYPE = 1; //系统类型 不能删除可以更改
    const EXTEND_TYPE = 2;//扩展类型 可以删除和更新
    Const READ_ONLY_TYPE = 0; //只读 
    
    protected $name = 'config';
    
    protected $likeColumn = ["key"];
    
    /**
     * 检查是否可以更改
     * @return boolean
     */
    public function isUpdateAble()
    {
        return ($this->getAttr("type") == self::EXTEND_TYPE || $this->getAttr("type") == self::SYSTEM_TYPE);
    }
    /**
     * 检查是否可以删除
     * @return boolean
     */
    public function isDeleteAble()
    {
        return ($this->getAttr("type") == self::EXTEND_TYPE);
    }
    
    /**
     * 是否是内置属性
     * @return boolean
     */
    public function isReadOnly()
    {
        return $this->getAttr("type") === self::READ_ONLY_TYPE;
    }
    
    /**
     * 删除
     * 
     * {@inheritDoc}
     * @see \think\Model::delete()
     */
    public function delete()
    {
        if(!$this->isDeleteAble()){
            throw new CommonException("",STATUS_CODE_SETTING_READONLY);
        }
        parent::delete();
    }
    
    
}