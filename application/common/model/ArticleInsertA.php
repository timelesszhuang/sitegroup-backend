<?php
/**
 * Created by PhpStorm.
 * User: qiangbi
 * Date: 17-4-26
 * Time: 下午2:25
 */

namespace app\common\model;
use think\Model;

class ArticleInsertA extends Model
{
    //只读字段
    protected $readonly=["node_id"];


    /**
     * 获取所有 文章
     * @param $limit
     * @param $rows
     * @param int $where
     * @return array
     */
    //TODO oldfunction
    public function getAll($limit,$rows,$where=0)
    {
        $count = $this->where($where)->count();
        $data = $this->limit($limit, $rows)->where($where)->select();
        return [
            "total" => $count,
            "rows" => $data
        ];
    }


}