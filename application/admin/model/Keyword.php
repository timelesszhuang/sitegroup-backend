<?php
/**
 * Created by PhpStorm.
 * User: qiangbi
 * Date: 17-4-26
 * Time: 下午2:25
 */

namespace app\admin\model;

use think\Model;

class Keyword extends Model
{
    /**
     * 根据tag获取数据
     * @param $tag
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getKeyword($tag)
    {
        $where["tag"]=$tag;
        $data=$this->where($where)->select();
        return $data;
    }



}