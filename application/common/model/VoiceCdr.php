<?php

namespace app\common\model;

use think\Model;

class VoiceCdr extends Model
{
    /**
     * 获取所有数据
     * @param $limit
     * @param $rows
     * @param $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author guozhen
     */
    //TODO oldfunction
    public function getAll($limit, $rows, $where)
    {
        $count = $this->where($where)->count();
        $data = $this->limit($limit, $rows)->where($where)->field('update_time',true)->order('id','desc')->select();
        return [
            "total" => $count,
            "rows" => $data
        ];

    }
}
