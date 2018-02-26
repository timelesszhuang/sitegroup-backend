<?php

namespace app\common\model;

use think\Model;

class Domain extends Model
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
    //TODO oldfunction
    public function getAll($limit,$rows,$where)
    {
        $count = $this->where($where)->count();
        $data = $this->limit($limit, $rows)->where($where)->order('id desc')->select();
        return [
            "total" => $count,
            "rows" => $data
        ];
    }
}
