<?php

namespace app\common\model;

use think\Model;

class Site extends Model
{
    //只读字段
    protected $readonly = ["node_id"];


    /**
     * 获取所有数据
     * @param $limit
     * @param $rows
     * @param $where
     * @return array
     * @author guozhen
     */
    //TODO oldfunction
    public function getAll($limit, $rows, $where)
    {
        $data = $this->where($where)->order('id', 'desc')->select();
        return [
            "rows" => $data
        ];
    }

    /**
     * 获取menu
     * @param $menu
     * @return string
     */
    //TODO oldfunction
    public function getMenuAttr($menu)
    {
        if(is_string($menu)){
            return trim($menu,",");
        }
    }

    /**
     * 格式化keyword
     * @param $key
     * @return string
     */
    //TODO oldfunction
    public function getKeywordIdsAttr($key)
    {
        if(is_string($key)){
            return trim($key,",");
        }
    }

    /**
     * 格式化keyword
     * @param $key
     * @return string
     */
    //TODO oldfunction
    public function getLinkIdAttr($key)
    {
        if(is_string($key)){
            return trim($key,",");
        }
    }

}
