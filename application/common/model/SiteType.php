<?php

namespace app\common\model;

use think\Model;

class SiteType extends Model
{
    //只读字段
    protected $readonly = ["node_id"];
    /**
     * 获取所有数据
     * @param $limit
     * @param $rows
     * @param $where
     * @return array
     * @author guozhen
     */
    //TODO oldfunction
    public function getAll($limit, $rows, $where)
    {
        $count = $this->where($where)->count();
        $data = $this->limit($limit, $rows)->where($where)->field('update_time', true)->order('id', 'desc')->select();
        return [
            "total" => $count,
            "rows" => $data
        ];

    }
}
