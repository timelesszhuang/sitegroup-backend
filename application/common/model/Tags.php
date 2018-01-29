<?php
/**
 * Created by PhpStorm.
 * User: qiangbi
 * Date: 17-4-26
 * Time: 下午2:25
 */

namespace app\common\model;

use app\common\traits\Osstrait;
use think\Config;
use think\Model;

class Tags extends Model
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
    //TODO oldfunction
    public function getList($limit, $rows, $where = 0)
    {
        $count = $this->where($where)->count();
        $data = $this->limit($limit, $rows)->where($where)->field('content,summary,update_time,readcount,title_color', true)->order('id desc')->select();
        return [
            "total" => $count,
            "rows" => $data
        ];
    }

}