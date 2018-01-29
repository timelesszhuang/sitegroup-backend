<?php

namespace app\common\model;

use think\Model;

class Rejection extends Model
{
    //只读字段
    protected $readonly=["node_id"];
    /**
     * 获取所有
     * @param $limit
     * @param $rows
     * @param int $where
     * @return array
     */
    //TODO oldfunction
    public function getAll($limit,$rows,$where)
    {
        $count = $this->where($where)->count();
        $data = $this->limit($limit, $rows)->where($where)->order('id desc')->field('field1')->field("update_time",true)->select();
        foreach ($data as $v){
            $v['Provincecities']=$v['region'].$v['city'];
        }
        return [
            "total" => $count,
            "rows" => $data
        ];
    }
}
