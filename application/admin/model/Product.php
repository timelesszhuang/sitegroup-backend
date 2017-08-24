<?php

namespace app\admin\model;

use think\Model;

class Product extends Model
{
    /**
     * 获取所有
     * @param $limit
     * @param $rows
     * @param int $where
     * @return array
     */
    public function getAll($limit, $rows, $where = 0)
    {
        $count = $this->where($where)->count();
        $data = $this->limit($limit, $rows)->where($where)->field('update_time,base64',true)->order('id desc')->select();
        return [
            "total" => $count,
            "rows" => $data
        ];
    }
}
