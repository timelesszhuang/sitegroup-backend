<?php

namespace app\common\model;

use think\Model;

class SiteLogo extends Model
{
    /**
     * 获取所有的数据
     * @param $limit
     * @param $rows
     * @param int $where
     * @return array
     */
    public function getAll($limit, $rows, $where = 0)
    {
        $count = $this->where($where)->count();
        $data = $this->limit($limit, $rows)->where($where)->field('static_time,update_time', true)->order('id desc')->select();
        return [
            "total" => $count,
            "rows" => $data
        ];
    }
}
