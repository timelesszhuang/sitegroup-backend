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
    public function getCompany($limit,$rows)
    {
        $count=$this->count();
        $data=$this->field("id,name,short_name,artificialperson,url,manbusiness,industry_id,create_time")->limit($limit,$rows)->order("id desc")->select();
        return [
            "total"=>$count,
            "rows"=>$data
        ];
    }
}