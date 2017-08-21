<?php

namespace app\admin\model;

use think\Model;

class UserDefinedForm extends Model
{
    /**
     * 获取
     * @param $limit
     * @param $rows
     * @param int $where
     * @return array
     */
    public function getAll($limit, $rows, $where = 0)
    {
        $count = $this->where($where)->count();
        $data = $this->limit($limit, $rows)->where($where)->field('form_info,update_time,',true)->order('id desc')->select();
        return [
            "total" => $count,
            "rows" => $data
        ];
    }

}
