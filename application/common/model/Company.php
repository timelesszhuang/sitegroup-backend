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
    public function getCompany()
    {
        $count=$this->count();
        $data=$this->select();



    }
}