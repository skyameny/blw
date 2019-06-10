<?php
/**
 * 检索类
 * 如果资源可被检索
 *
 * User: keepwin100
 * Time: 22:52
 */
namespace core\service\traits;

use core\utils\ExLog;
use think\Config;

trait Searchable
{
    public function searchInstances($condition = [])
    {
        $returnValue = [];
        $s_where = [];
        if (isset($condition["keywords"]) && ! empty($condition["keywords"])) {
            if (! empty($this->likeColumn)) {
                $like_key = implode($this->likeColumn, "|");
                $s_where["$like_key"] = [
                    "like",
                    "%" . trim($condition["keywords"]) . "%"
                ];
            }
        }
        $ispaginate = !empty($condition["page"]);
        unset($condition["keywords"]);
        $page = $condition["page"] ?$condition["page"]: 0;
        $sort = $condition["sort"] ?$condition["sort"]: "id desc";
        $limit = $condition["limit"] ?$condition["limit"]: Config::get("paginate.list_rows");
        unset($condition["page"]);
        unset($condition["limit"]);
        unset($condition["sort"]);
        $s_where = array_merge($s_where, $condition);
        $call_class = get_called_class();
        $sc_model = new $call_class();
        if ($ispaginate) {
            $returnValue["count"] = $sc_model->where($s_where)->count();
        }
        $query = $sc_model->where($s_where);
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