<?php
/**
 * Created by PhpStorm.
 * User: qiangbi
 * Date: 17-4-26
 * Time: 下午2:25
 */

namespace app\common\model;

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
    //TODO oldfunction
    public function getMenu($limit, $rows, $where)
    {
        $count = $this->where($where)->count();
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
    //TODO oldfunction
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

    /**
     * 获取站点的对应的 type_id
     * @param $menu_id_array
     * @param $menu_flag
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    //TODO oldfunction
    public function getSiteTypeIds($menu_id_array,$menu_flag)
    {
        $whe['flag'] = $menu_flag;
        $data = $this->where($whe)->where('id', 'in', $menu_id_array);
        foreach (array_filter(explode(',', $menu_id_array)) as $menu_id) {
            $data = $data->whereOr('path', 'like', "%,$menu_id,%");
        }
        $data = $data->field('type_id,type_name,tag_name');
        $data = $data->select();
        $type_ids = [];
        foreach ($data as $k => $v) {
            foreach (array_filter(explode(',', $v['type_id'])) as $value) {
                $type_ids[$value] = 1;
            }
        }
        $type_ids = array_keys($type_ids);
        return $type_ids;
    }

}