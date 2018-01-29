<?php

namespace app\common\model;

use think\Model;

class Producttype extends Model
{
    /**
     * 获取所有
     * @param $limit
     * @param $rows
     * @param int $where
     * @return array
     */
    //TODO oldfunction
    public function getAll($limit, $rows, $where = 0)
    {
        $count = $this->where($where)->count();
        $data = $this->limit($limit, $rows)->where($where)->field('update_time',true)->order('id desc')->select();
        return [
            "total" => $count,
            "rows" => $data
        ];
    }
    /**
     * @param int $where
     * @return false|\PDOStatement|string|\think\Collection
     */
    //TODO oldfunction
    public function getArttype($where=0)
    {
        $data =$this->alias('type')->field('type.id,name,detail,tag_id,tag')->join('type_tag','type_tag.id = tag_id','LEFT')->where($where)->select();
        return $data;
    }
}
