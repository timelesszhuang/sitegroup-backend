<?php

namespace app\common\model;

use think\Model;

class UserDefinedForm extends Model
{
    //只读字段
    protected $readonly = ["node_id"];

    /**
     * 获取
     * @param $limit
     * @param $rows
     * @param int $where
     * @return array
     */
    //TODO oldfunction
    public function getAll($limit, $rows, $where = 0)
    {
        $count = $this->where($where)->count();
        $data = $this->limit($limit, $rows)->where($where)->field('form_info,update_time,', true)->order('id desc')->select();

        return [
            "total" => $count,
            "rows" => $data
        ];
    }
    //获取所有类型
    //TODO oldfunction
    public function getForm($where)
    {
        $data = $this->where($where)->field('id,detail')->select();
        return $data;
    }
}
