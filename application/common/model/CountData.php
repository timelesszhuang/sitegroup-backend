<?php

namespace app\common\model;

use app\common\model\Article;
use app\common\model\Pv;
use app\common\model\Rejection;
use app\common\model\Useragent;
use think\Model;

class CountData extends Model
{
    /**
     * 统计浏览量
     */
    public function countPv($node_id, $ttime)
    {
        return (new Pv)->where(["node_id" => $node_id, "create_time" => ["egt", $ttime]])->count();
    }

    /**
     * 统计浏览量
     * @param int $node_id
     * @return int|string
     */
    public function countSite($node_id = 0)
    {
        $where = [];
        if ($node_id != 0) {
            $where['node_id'] = $node_id;
        }
        return (new Site())->where($where)->count();
    }

    /**
     * 统计客户数
     * @return int|string
     */
    public function countCustomer()
    {
        return (new Node())->count();
    }

    /**
     * 获取爬虫信息
     * @param $node_id
     * @param $ttime
     * @return int|string
     */
    public function countUseragent($node_id, $ttime)
    {
        return (new Useragent)->where(["node_id" => $node_id, "create_time" => ["egt", $ttime]])->count();
    }

    /**
     * 获取文章添加数量
     * @param $node_id
     * @param $ttime
     * @return int|string
     */
    public function countArticle($node_id = 0, $ttime = 0)
    {
        $where = [];
        if ($node_id != 0) {
            $where['node_id'] = $node_id;
        }
        if ($ttime != 0) {
            $where['create_time'] = ["egt", $ttime];
        }
        return (new Article)->where($where)->count();
    }

    /**
     * 获取甩单数量
     * @param $node_id
     * @param $ttime
     * @return int|string
     */
    public function countShuaidan($node_id, $ttime)
    {
        return (new Rejection)->where(["node_id" => $node_id, "create_time" => ["egt", $ttime]])->count();
    }

    /**
     * 收录数量
     * @param $node_id
     * @return float|int
     */
    public function countInclude($node_id = 0)
    {
        $where = [];
        if ($node_id != 0) {
            $where['node_id'] = $node_id;
        }
        $count = (new ArticleSearchengineInclude)->where(["node_id" => $node_id])->sum("count");
        return $count;
    }
}
