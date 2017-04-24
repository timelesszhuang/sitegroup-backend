<?php
/**
 * Created by PhpStorm.
 * User: 赵甲戌
 * Date: 2017/4/21
 * Time: 11:35
 */
namespace app\common\model;

use think\Model;

class Industry extends Model{
    /**
     * 分页
     * @param $limit
     * @param $rows
     * @return array
     * @auther jingzheng
     */
    public function getIndustry($limit, $rows)
    {
        $count = $this->count();
        $data = $this->limit($limit, $rows)->order("id", "desc")->field("id,name,detail,create_time,update_time")->select();
        return [
            "total" => $count,
            "rows" => $data
        ];
    }
    public function getSort()
    {
        $data = $this->order("sort", "desc")->field("id,name")->select();
        return $data;
    }


}