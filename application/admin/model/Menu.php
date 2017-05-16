<?php
/**
 * Created by PhpStorm.
 * User: qiangbi
 * Date: 17-4-26
 * Time: 下午2:25
 */

namespace app\admin\model;
use think\Model;

class Menu extends Model
{
    //只读字段
    protected $readonly=["node_id"];
    /**
     * @param $limit
     * @param $rows
     * @param $where
     * @return array
     */
    public function getMenu($limit,$rows,$where)
    {
        $count = $this->where($where)->count();
        $data = $this->limit($limit, $rows)->where($where)->select();
        return [
            "total" => $count,
            "rows" => $data
        ];
    }
}