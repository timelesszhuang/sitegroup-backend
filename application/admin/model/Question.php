<?php

namespace app\admin\model;

use think\Model;

class Question extends Model
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
        $data = $this->limit($limit, $rows)->where($where)->field('create_time,update_time',true)->order('id','desc')->select();
        return [
            "total" => $count,
            "rows" => $data
        ];

    }
}
