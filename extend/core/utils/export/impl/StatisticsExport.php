<?php
/**
 * 导出统计报表
 * User: Dream<hukaijun@emicnet.com>
 * Date: 2019-03-22
 * Time: 11:15
 */

namespace core\utils\export\impl;

use aicallup\model\Enterprise;
use aicallup\model\Script;
use aicallup\model\Task;
use aicallup\service\EnterpriseService;
use core\utils\ExLog;
use core\utils\export\EmExport;
use core\utils\export\exception\ExportException;

final class StatisticsExport extends EmExport
{
    protected function setDefaultFileName()
    {
        $eid = $this->getParam("eid");
        $script_id =$this->getParam("script_id");
        $task_id = $this->getParam("task_id");
        $enterprise = Enterprise::get($eid);
        $str = [];
        $str_t = "";
        if(!empty($script_id)){
            $script_name = Script::get($script_id)->getAttr("name");
            $str[]= $script_name."话术";
        }
        if(!empty($task_id)){
            $task_name = Task::get($task_id)->getAttr("name");
            $str[]= $task_name."任务";
        }
        if(!empty($str)){
            $str_t = "(".implode(",",$str).")";
        }
        return sprintf("%s外呼任务统计表%s",$enterprise->getAttr("name"),$str_t);

    }

    protected function setDefaultTitle()
    {
        $eid = $this->param["eid"];
        $enterprise = Enterprise::get($eid);
        $epName = $enterprise->getAttr("name");
        return  "企业[$epName]外呼统计";
    }

    protected function setDefaultHeader()
    {
        return [
            "外呼日期",
            "总外呼量",
            "接通数",
            "接通率",
            "接听即挂断",
            "接听即挂断占比",
            "30s以上通话",
            "30s以上通话占比",
            "平均通话时长（s）",
            "A类用户",
            "B类用户",
            "AB类用户占比",
            "发短信客户数",
            "转人工数量",
            "转人工占比",
            "转接成功",
            "转接失败",
            "转接成功占比",
            "未接听",
            "未接听占比",
            "空号",
            "空号占比",
            "停机",
            "停机占比",
            "关机",
            "关机占比",
            "占线",
            "占线占比",
            "拒接",
            "拒接占比",
            "备注",
        ];
    }

    /**
     * 设置数据
     * @param array $params 这个参数是为了携带动态参数
     * @return array
     * @throws ExportException
     * @throws \think\exception\DbException
     */
    public function setData($params = []){
        $format_data = [];
        if(empty($params)){
            $params = $this->param;
        }
        $enterpriseService = app(EnterpriseService::class, [$params["eid"]]);
        $requestParams = $params;
        $enterprise = Enterprise::get($params["eid"]);
        if(is_null($enterprise)){
            throw new ExportException("企业不存在",PARAM_ERROR);
        }
        $statistics = $enterpriseService->getStatisticsV2($enterprise,$requestParams,true);
        if(empty($statistics) || !is_array($statistics)){
            return $format_data;
        }
        $format_data = $this->format_data($statistics);
        return $format_data;
    }

    /**
     * 格式化 导出数据
     * @param $duration_data
     * @return array|bool
     */
    private function format_data($duration_data)
    {
        $report = [];
        if(empty($duration_data) || !is_array($duration_data))
        {
            return false;
        }
        foreach ( $duration_data as $d =>$item){
            $r_data = [];
            //总呼叫数
            $total_call_count = $item["total_call_count"];
            //接通数
            $result_call_answered = $item["result_call_answered"]+$item["result_call_answered_and_hangup"];
            //接通挂断数
            $answered_and_hangup = $item["result_call_answered_and_hangup"];
            //接通率
            $answered_rate =($total_call_count ==0)?0:sprintf("%.2f",$result_call_answered*100/$total_call_count);
            //接通挂断率
            $answered_and_hangup_rate = ($total_call_count ==0)?0:sprintf("%.2f",$answered_and_hangup*100/$total_call_count);
            //30以上通话
            $duration_30_up = $item["duration_30_up"];
            $duration_30_up_rate = ($result_call_answered ==0)?0:sprintf("%.2f",$duration_30_up*100/$result_call_answered);
            //平均外呼时间
            $total_call_duration = $item["total_call_duration"];
            $duration_avg = ($result_call_answered ==0)?0:sprintf("%.2f",$total_call_duration/$result_call_answered);
            //A，B类用户
            $intention_a_count = intval($item["intention_a_count"]);
            $intention_b_count = intval($item["intention_b_count"]);
            //AB类用户占比
            $intention_ab_count = $intention_a_count+$intention_b_count;
            $intention_ab_rate =($total_call_count ==0)?0:sprintf("%.2f",$intention_ab_count*100/$total_call_count);
            //短信数量
            $message_count = intval($item["message_count"]);
            //转人工 成功失败
            $manual_failed = intval($item["manual_failed"]);
            $manual_success = intval($item["manual_success"]);
            $manual_count = $manual_failed+$manual_success;
            //转接占比
            $manual_rate =($result_call_answered ==0)?0:sprintf("%.2f",$manual_count*100/$result_call_answered);
            //转接成功占比
            $manual_success_rate =($manual_count ==0)?0:sprintf("%.2f",$manual_success*100/$manual_count);
            $horary_date_day = $item["horary_date_day"];
            //未接听场景
            $result_call_not_answered = intval($item["result_call_not_answered"]);
            $call_not_answered_rate = ($total_call_count ==0)?0:sprintf("%.2f",$result_call_not_answered*100/$total_call_count);
            //空号
            $result_call_number_not_find = intval($item["result_call_number_not_find"]);
            $result_call_number_not_find_rate = ($total_call_count ==0)?0:sprintf("%.2f",$result_call_number_not_find*100/$total_call_count);
            //停机
            $result_call_downtime = intval($item["result_call_downtime"]);
            $result_call_downtime_rate = ($total_call_count ==0)?0:sprintf("%.2f",$result_call_downtime*100/$total_call_count);
            //关机
            $result_call_shutdown = intval($item["result_call_shutdown"]);
            $result_call_shutdown_rate = ($total_call_count ==0)?0:sprintf("%.2f",$result_call_shutdown*100/$total_call_count);
            //占线
            $result_call_busy_line = intval($item["result_call_busy_line"]);
            $result_call_busy_line_rate = ($total_call_count ==0)?0:sprintf("%.2f",$result_call_busy_line*100/$total_call_count);
            //拒接
            $result_call_rejected = intval($item["result_call_rejected"]);
            $result_call_rejected_rate = ($total_call_count ==0)?0:sprintf("%.2f",$result_call_rejected*100/$total_call_count);

            //输出层 按顺序
            $r_data[] = $horary_date_day;
            $r_data[] = $total_call_count;
            $r_data[] = $result_call_answered;
            $r_data[] = $answered_rate."%";
            $r_data[] = $answered_and_hangup;
            $r_data[] = $answered_and_hangup_rate."%";
            $r_data[] = $duration_30_up;
            $r_data[] = $duration_30_up_rate."%";
            $r_data[] = $duration_avg;
            $r_data[] = $intention_a_count;
            $r_data[] = $intention_b_count;
            $r_data[] = $intention_ab_rate."%";
            $r_data[] = $message_count;
            $r_data[] = $manual_count;
            $r_data[] = $manual_rate."%";
            $r_data[] = $manual_success;
            $r_data[] = $manual_failed;
            $r_data[] = $manual_success_rate."%";
            $r_data[] = $result_call_not_answered;
            $r_data[] = $call_not_answered_rate."%";
            $r_data[] = $result_call_number_not_find;
            $r_data[] = $result_call_number_not_find_rate."%";
            $r_data[] = $result_call_downtime;
            $r_data[] = $result_call_downtime_rate."%";
            $r_data[] = $result_call_shutdown;
            $r_data[] = $result_call_shutdown_rate."%";
            $r_data[] = $result_call_busy_line;
            $r_data[] = $result_call_busy_line_rate."%";
            $r_data[] = $result_call_rejected;
            $r_data[] = $result_call_rejected_rate."%";

            $r_data[] = "无";
            $report[]  = $r_data;
        }
        return $report;
    }

    /**
     * 如果不是流式传输返回false
     * @return int
     */
    public function getTotalCount()
    {
        return false;
    }

}

#调用


