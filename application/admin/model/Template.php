<?php
/**
 * Created by PhpStorm.
 * User: timeless
 * Date: 17-5-15
 * Time: 下午17:25
 */

namespace app\admin\model;

use think\Model;

class Template extends Model
{
    /**
     * 获取所有 模板
     * @param $limit
     * @param $rows
     * @param int $where
     * @return array
     */
    public function getTemplate($limit, $rows, $where = 0)
    {
        $count = $this->where($where)->count();
        $data = $this->limit($limit, $rows)->where($where)->select();
        return [
            "total" => $count,
            "rows" => $data
        ];
    }
}