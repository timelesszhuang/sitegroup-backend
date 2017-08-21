<?php

namespace app\common\model;

use think\Model;

class SiteErrorInfo extends Model
{
    //只读字段
    protected $readonly=["node_id"];
    /**
     * @param $limit
     * @param $rows
     * @param $where
     * @return array
     */
    public function getAll($limit,$rows,$where)
    {
        $count = $this->where($where)->count();
        $data = $this->limit($limit, $rows)->where($where)->field("id,msg,operator,site_name,status,create_time")->order("id","desc")->select();
        return [
            "total" => $count,
            "rows" => $data
        ];
    }

    /**
     * 设置已读 未读
     * @param $status
     * @return string
     */
    public function getStatusAttr($status)
    {
        if($status==10){
            return "已读";
        }else if($status==20){
            return "未读";
        }
    }
}
