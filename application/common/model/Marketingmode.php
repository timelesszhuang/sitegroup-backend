<?php

namespace app\common\model;

use think\Model;

class Marketingmode extends Model
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

    /**
     * 格式化时间
     * @param $key
     * @return false|string
     */
    //TODO oldfunction
    public function getCreateTimeAttr($key)
    {
        if(!empty($key)){
            return date("y-m-d H:i",$key);
        }
    }
}
