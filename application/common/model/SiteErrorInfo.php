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
        $data = $this->limit($limit, $rows)->where($where)->field("msg,operator,site_name")->select();
        return [
            "total" => $count,
            "rows" => $data
        ];
    }
}
