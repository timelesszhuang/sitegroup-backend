<?php

namespace app\common\model;

use think\Model;

class QuestionType extends Model
{
    //只读字段
    protected $readonly = ["node_id"];
    /**
     * @param int $where
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    //TODO oldfunction
    public function getArttype($where = 0)
    {
        $data = $this->alias('type')->field('type.id,name,alias,tag_id,tag')->join('type_tag', 'type_tag.id = tag_id','LEFT')->where($where)->select();
        return $data;
    }
}
