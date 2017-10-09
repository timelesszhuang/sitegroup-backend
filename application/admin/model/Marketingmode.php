<?php

namespace app\admin\model;

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
    public function getList($limit, $rows,$where=0)
    {
        $count=$this->where($where)->count();
        $data = $this->limit($limit, $rows)->order("id", "desc")->field("id,readcount,title,create_time,industry_name,keyword,img")->where($where)->select();
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
    public function getCreateTimeAttr($key)
    {
        if(!empty($key)){
            return date("y-m-d H:i",strtotime($key));
        }
    }
}
