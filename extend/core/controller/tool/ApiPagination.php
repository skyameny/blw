<?php
namespace core\controller\tool;

use think\Config;
use think\Request;

trait ApiPagination
{
    //分页参数
    function paginationParams($allow_params=[])
    {
        $returnValue = [];
        $pagination_configs =  Config::get("api_pagination"); 
        if(empty($pagination_configs)){
            //默认分页查询字段
            $pagination_configs = ["page"=>"page","limit"=>"limit","keywords"=>"search","sort"=>"sort"];
        }
        $request = Request::instance();
        foreach ($pagination_configs as $_key=> $_value)
        {
            if($request->has($_value)){
                $returnValue[$_key] = $request->param($_value);
            }
        }
        foreach ($allow_params as $_value){
            if($request->has($_value)){
                $returnValue[$_value] = $request->param($_value);
            }  
        }
        return $returnValue;
    }
}