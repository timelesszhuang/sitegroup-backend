<?php

namespace app\admin\model;

use think\Model;

class ScatteredTitle extends Model
{
    /**
     * 获取所有数据
     * @param $limit
     * @param $rows
     * @param $where
     * @return array
     * @auther guozhen
     */
    public function getAll($limit, $rows, $where)
    {
        $count = $this->where($where)->count();
        $data = $this->limit($limit, $rows)->where($where)->order('id','desc')->select();
        return [
            "total" => $count,
            "rows" => $data
        ];

    }
}
