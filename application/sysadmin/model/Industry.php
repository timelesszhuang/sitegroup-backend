<?php
/**
 * Created by PhpStorm.
 * User: jingzheng
 * Date: 2017/4/21
 * Time: 11:35
 */
namespace app\sysadmin\model;

use think\Model;

class Industry extends Model{
    /**
     * 分页
     * @param $limit
     * @param $rows
     * @return array
     * @auther jingzheng
     */
    public function getIndustry($limit, $rows,$where=0)
    {
        $count=$this->where($where)->count();
        $data = $this->limit($limit, $rows)->order("id", "desc")->where($where)->select();
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