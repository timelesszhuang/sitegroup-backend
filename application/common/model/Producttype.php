<?php

namespace app\common\model;

use think\Model;

class Producttype extends Model
{
    /**
     * @param int $where
     * @return false|\PDOStatement|string|\think\Collection
     */
    //TODO oldfunction
    public function getArttype($where=0)
    {
        $data =$this->alias('type')->field('type.id,name,detail,tag_id,tag')->join('type_tag','type_tag.id = tag_id','LEFT')->where($where)->select();
        return $data;
    }
}
