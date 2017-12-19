<?php
/**
 * Created by PhpStorm.
 * User: qiangbi
 * Date: 17-4-26
 * Time: 下午2:25
 */

namespace app\admin\model;

use app\common\traits\Osstrait;
use think\Config;
use think\Model;

class Contentget extends Model
{
    use Osstrait;
    //只读字段
    protected $readonly = ["node_id"];

    /**
     * 获取所有 图集
     * @param $limit
     * @param $rows
     * @param int $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getContentList($limit, $rows, $where = 0)
    {
        $count = $this->where($where)->count();
        $data = $this->limit($limit, $rows)->where($where)->field('name,en_name,content,href', true)->order('id desc')->select();
        return [
            "total" => $count,
            "rows" => $data
        ];
    }

}