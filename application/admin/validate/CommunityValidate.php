<?php
/**
 * 验证器
 * @author Dream<hukaijun@emicnet.com>
 */
namespace app\admin\validate;

class CommunityValidate extends BlAdminValidate
{
    protected $rule = array(
        'cid'               =>"require|number",
        "cname"              =>"require|max:255",
    );
    
    protected $message = array(
        'cid.require'    => STATUS_CODE_COMMUNITY_NOT_FOUND,
        'cid.number'    => STATUS_CODE_COMMUNITY_NOT_FOUND,
        'cname.require' =>"请填写社区名称",
        'cname.max'     =>"名字太长了",
        
        
    );
    
    protected  $scene = array(
        //startTask
        'getinfo' =>["cid"],
        'regiter'=>["cname","address"],
        
    );
}