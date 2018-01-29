<?php

namespace app\common\model;

use think\Model;

class CaseCenter extends Model
{
    /**
     * 分页
     * @param $limit
     * @param $rows
     * @return array
     * @author jingzheng
     */
    //TODO oldfunction
    public function getList($limit, $rows,$where=0)
    {
        $count=$this->where($where)->count();
        $data = $this->limit($limit, $rows)->order("id", "desc")->field("update_time",true)->where($where)->select();
        return [
            "total" => $count,
            "rows" => $data
        ];
    }
}
