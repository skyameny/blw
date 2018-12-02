<?php
namespace core\model;

use think\Model;
use think\Config;
use core\utils\ExLog;

/**
 * Bl项目基类模型
 *
 * @author Dream<1015617245@qq.com>
 *        
 */
abstract class BlModel extends Model
{
    protected $likeColumn = [];
    
    public static function getComunity()
    {
        return defined("COMMUNITY_IDENTITY") ? COMMUNITY_IDENTITY : null;
    }

    /**
     * 查找对象
     * 
     * @param unknown $map            
     * @return boolean|\think\static[]|\think\false
     */
    public function searchInstances($condition = [])
    {
        $returnValue = [];
        $swhere = [];
        if (isset($condition["keywords"]) && ! empty($condition["keywords"])) {
            if (! empty($this->likeColumn)) {
                $like_key = implode($this->likeColumn, "|");
                $swhere["$like_key"] = [
                    "like",
                    "%" . trim($condition["keywords"]) . "%"
                ];
            }
        }
        $ispaginate = !empty($condition["page"]);
        unset($condition["keywords"]);
        $page = $condition["page"] ?? 0;
        $sort = $condition["sort"] ?? "id desc";
        $limit = $condition["limit"] ?? Config::get("paginate.list_rows");
        unset($condition["page"]);
        unset($condition["limit"]);
        unset($condition["sort"]);
        $swhere = array_merge($swhere, $condition);
        $call_class = get_called_class();
        $sc_model = new $call_class();
        if ($ispaginate) {
            $returnValue["count"] = $sc_model->where($swhere)->count();
        }
        $query = $sc_model->where($swhere);
        if (! empty($page) && ! empty($limit)) {
            $start = ($page - 1) * $limit;
            $query = $query->limit($start, $limit);
        }
        if (! empty($sort)) {
            $query = $query->order($sort);
        }
        $returnValue["content"] = $query->select();
        ExLog::log("search sql:".$this->getLastSql(),ExLog::DEBUG);
        return $ispaginate ? $returnValue : $returnValue["content"];
    }
    
}