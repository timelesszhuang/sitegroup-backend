<?php

namespace app\admin\model;

use think\Model;

class Industry extends Model
{
    public function getSort()
    {
        $data = $this->order("sort", "desc")->field("id,name")->select();
        return $data;
    }
}
