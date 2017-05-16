<?php

namespace app\admin\model;

use think\Model;

class ScatteredTitle extends Model
{
    //只读字段
    protected $readonly=["node_id"];
    const COUNT = 15;
    const RAND = 8;
    const LIMIT = 7;
    /**
     * 初始化静态函数
     * @author guozhen
     */
    public static function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
        // 在新添加和修改前 随机获取文章id
        ScatteredTitle::event('before_write', function ($stitle) {
            $stitle->article_ids = self::randomAds($stitle->node_id, $stitle->articletype_id);
        });
    }

    /**
     * 随机获取文章id
     * @param $node_id
     * @param $articletype
     * @return int|string
     * @author guozhen
     */
    public static function randomAds($node_id, $articletype)
    {
        $Smodel = new ScatteredArticle();
        //取出当前node_id articletype_id 的文章总数
        $count = $Smodel->where(["node_id" => $node_id, "articletype_id" => $articletype])->count();
        //如果总数大于15的话 我们才随机给予文章id
        if ($count > self::COUNT) {
            $rand = mt_rand(0, $count - self::RAND);
            $limit = $rand . "," . self::LIMIT;
            $data = $Smodel->where(["node_id" => $node_id, "articletype_id" => $articletype])->limit($limit)->field("id")->select();
            $data_arr = collection($data)->toArray();
            return implode(",", array_column($data_arr, "id"));
        }
        // 否则给予 0
        return 0;
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
}
