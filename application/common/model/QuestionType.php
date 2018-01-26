<?php

namespace app\admin\model;

use think\Model;

class QuestionType extends Model
{
    //只读字段
    protected $readonly = ["node_id"];

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
    public function getAll($limit, $rows, $where)
    {
        $count = $this->where($where)->count();
        $data = $this->limit($limit, $rows)->where($where)->order('id', 'desc')->select();
        return [
            "total" => $count,
            "rows" => $data
        ];

    }

    /**
     * @param int $where
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getArttype($where = 0)
    {
        $data = $this->alias('type')->field('type.id,name,alias,tag_id,tag')->join('type_tag', 'type_tag.id = tag_id','LEFT')->where($where)->select();
        return $data;
    }
}
