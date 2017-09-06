<?php

namespace app\admin\model;

use think\Model;

class SystemNotice extends Model
{
    /**
     * 获取表格数据
     * @param $limit
     * @param $rows
     * @param int $where
     * @return array
     */
    public function getList($limit, $rows, $where = 0)
    {
        $count = $this->where($where)->count();
        $data = $this->limit($limit, $rows)->where($where)->field('update_time',true)->order('id desc')->select();
        return [
            "total" => $count,
            "rows" => $data
        ];
    }
}
