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
    protected $rule = [
        ['name', 'require', '公司名必须'],
        ['detail', 'require', '详细必须'],
    ];
    public function getIndustry($limit, $rows)
    {
        $count = $this->count();
        $data = $this->limit($limit, $rows)->order("id", "desc")->field("id,name,detail,create_time,update_time")->select();
        return [
            "total" => $count,
            "rows" => $data
        ];
    }



}