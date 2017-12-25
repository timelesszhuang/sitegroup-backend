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
    protected $readonly = ["node_id"];

    /**
     * @param $limit
     * @param $rows
     * @param $where
     * @return array
     */
    public function getMenu($limit, $rows, $where)
    {
        $count = $this->where($where)->count();
        $where['p_id']=0;
        $data = $this->limit($limit, $rows)->where($where)->order('id desc,sort asc')->select();
        return [
            "total" => $count,
            "rows" => $data
        ];
    }

    /**
     *
     *  获取栏目名称 分类 详情数据
     * @param $where
     * @param $field
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getlist($where, $field)
    {
        $data = $this->where($where)->field($field)->select();
        foreach ($data as $k => $v) {
            if (!empty(trim($v['type_name']))) {
                $v['typeName'] = '—' . $v['type_name'];
            }
        }
        return $data;
    }

//    /**
//     * 获取栏目分类
//     */
//    public function getmenutype()
//    {
//
//
//    }

}