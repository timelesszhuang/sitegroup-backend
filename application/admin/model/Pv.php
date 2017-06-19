<?php

namespace app\admin\model;

use think\Model;

class   Pv extends Model
{
    //只读字段
    protected $readonly=["node_id"];
    /**
     * 获取所有代码
     * @param $limit
     * @param $rows
     * @param int $where
     * @return array
     */
    public function getAll($limit,$rows,$where)
    {
        $count = $this->where($where)->count();
        $data = $this->limit($limit, $rows)->where($where)->field("create_time,update_time",true)->select();
        foreach ($data as $v){
           $v['Provincecities']=$v['region'].$v['city'];
        }
        return [
            "total" => $count,
            "rows" => $data
        ];
    }
}
