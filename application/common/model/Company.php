<?php
/**
 * Created by PhpStorm.
 * User: 赵甲戌
 * Date: 2017/4/21
 * Time: 11:34
 */

namespace app\common\model;
use think\Model;
class Company extends Model
{
    /**
     * 获取全部公司信息
     * @param $limit
     * @param $rows
     * @return array
     * @auther guozhen
     */
    public function getCompany($limit,$rows,$where=0)
    {
        $count=$this->count();
        $data=$this->limit($limit,$rows)->order("id desc")->where($where)->select();
        return [
            "total"=>$count,
            "rows"=>$data
        ];
    }
}