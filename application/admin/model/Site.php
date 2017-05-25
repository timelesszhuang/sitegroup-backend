<?php

namespace app\admin\model;

use think\Model;

class Site extends Model
{
    //只读字段
    protected $readonly = ["node_id"];

    // 初始化操作
    public static function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
        Site::event("before_write", function ($site) {
            if($site->menu){
                $site->menu = "," . implode(",",$site->menu) . ",";
            }
            if($site->keyword_ids){
                $site->keyword_ids = "," . implode(",",$site->keyword_ids) . ",";
            }
        });
    }


    /**
     * 获取所有数据
     * @param $limit
     * @param $rows
     * @param $where
     * @return array
     * @author guozhen
     */
    public function getAll($limit, $rows, $where)
    {
        $count = $this->where($where)->count();
        $data = $this->limit($limit, $rows)->where($where)->order('id', 'desc')->select();
        return [
            "total" => $count,
            "rows" => $data
        ];
    }

    /**
     * 获取menu
     * @param $menu
     * @return string
     */
    public function getMenuAttr($menu)
    {
        dump($menu);die;
        return trim($menu,",");
    }

    /**
     * 格式化keyword
     * @param $key
     * @return string
     */
    public function getKeywordIdsAttr($key)
    {
        return trim($key,",");
    }
}
