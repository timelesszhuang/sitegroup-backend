<?php

namespace app\admin\model;

use think\Model;

class Code extends Model
{
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
        $data = $this->limit($limit, $rows)->where($where)->select();
        return [
            "total" => $count,
            "rows" => $data
        ];
    }
}
