<?php
/**
 * Created by PhpStorm.
 * User: 赵甲戌
 * Date: 2017/4/21
 * Time: 11:34
 */

namespace app\common\model;
class Company extends Model
{
    public function getCompany($limit,$rows)
    {
        $count=$this->count();
        $data=$this->field("id,name,sort_name,artificialperson,url,manbusiness,industry_id,create_time")->limit($limit,$rows)->order("id desc")->select();



    }
}