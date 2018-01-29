<?php

namespace app\common\model;

use think\Model;

class Product extends Model
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
        $data = $this->limit($limit, $rows)->where($where)->field('update_time,base64',true)->order('id desc')->select();
        return [
            "total" => $count,
            "rows" => $data
        ];
    }


    //TODO oldfunction
    public function getProducttdk($limit, $rows, $w = '',$wheretype_id='')
    {

        $count = $this->where('type_id', 'in', $wheretype_id)->where($w)->count();
        $productdata = $this->limit($limit, $rows)->where('type_id', 'in', $wheretype_id)->where($w)->field('id,name,create_time')->order('id desc')->select();
        return [
            "total" => $count,
            "rows" => $productdata
        ];
    }

}
