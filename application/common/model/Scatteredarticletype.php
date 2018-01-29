<?php

namespace app\common\model;

use think\Model;

class Scatteredarticletype extends Model
{
    /**
     * 获取所有分类
     * @param $limit
     * @param $rows
     * @param int $where
     * @return array
     */
    //TODO oldfunction
    public function getTypes($limit, $rows, $where = 0)
    {
        $count = $this->where($where)->count();
        $data = $this->limit($limit, $rows)->where($where)->field('id,name,create_time,detail')->order('id desc')->select();
        return [
            "total" => $count,
            "rows" => $data
        ];
    }
}
