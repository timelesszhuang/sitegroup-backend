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
     * 获取文章添加数量
     * @param $node_id
     * @param $ttime
     * @return int|string
     */
    public function countProduct($node_id = 0, $ttime = 0)
    {
        $where = [];
        if ($node_id != 0) {
            $where['node_id'] = $node_id;
        }
        if ($ttime != 0) {
            $where['create_time'] = ["egt", $ttime];
        }
        return (new Product())->where($where)->count();
    }

    /**
     * 获取文章添加数量
     * @param $node_id
     * @param $ttime
     * @return int|string
     */
    public function countQuestion($node_id = 0, $ttime = 0)
    {
        $where = [];
        if ($node_id != 0) {
            $where['node_id'] = $node_id;
        }
        if ($ttime != 0) {
            $where['create_time'] = ["egt", $ttime];
        }
        return (new Question())->where($where)->count();
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
        $count = (new ArticleSearchengineInclude)->where($where)->sum("count");
        return $count;
    }

    /**
     * @return array
     * 关键词统计
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function keywordCount()
    {
        $keyword = (new Keyword());
        $arr = $keyword->field('tag,count(id) as tagCount')->group('tag')->order("tagCount", "desc")->select();
        $te = [];
        $ar = [];
        foreach ($arr as $k => $v) {
            $te[] = $v['tagCount'];
            $ar[] = $v['tag'];
        }
        /** @var string $ar */
        $temp = ["count" => $te, "name" => $ar];
        //return $this->resultArray($temp);
        return $te;
    }


    /**
     * 统计浏览量
     */
    public function sitecountPv($siteinfo, $ttime)
    {
        $site_id = $siteinfo['id'];
        $node_id = $siteinfo['node_id'];
        return Pv::where(["node_id" => $node_id, "site_id" => $site_id, "create_time" => ["egt", $ttime]])->count();
    }

    /**
     * 统计root浏览量
     */
    public function rootcountPv($ttime)
    {
        return Pv::where(["create_time" => ["egt", $ttime]])->count();
    }

    /**
     * 获取爬虫信息
     * @param $node_id
     * @param $ttime
     * @return int|string
     */
    public function sitecountUseragent($siteinfo, $ttime)
    {
        $site_id = $siteinfo['id'];
        $node_id = $siteinfo['node_id'];
        return Useragent::where(["node_id" => $node_id, "site_id" => $site_id, "create_time" => ["egt", $ttime]])->count();
    }

    /**
     * 获取文章添加数量
     * @param $node_id
     * @param $ttime
     * @return int|string
     */
    public function sitecountArticle($siteinfo, $ttime)
    {
        $site_id = $siteinfo['id'];
        $node_id = $siteinfo['node_id'];
        return Article::where(["node_id" => $node_id, "site_id" => $site_id, "create_time" => ["egt", $ttime]])->count();
    }

    /**
     * 获取甩单数量
     * @param $node_id
     * @param $ttime
     * @return int|string
     */
    public function sitecountShuaidan($siteinfo, $ttime)
    {
        $site_id = $siteinfo['id'];
        $node_id = $siteinfo['node_id'];
        return Rejection::where(["node_id" => $node_id, "site_id" => $site_id, "create_time" => ["egt", $ttime]])->count();
    }

    /**
     * 收录数量
     * @param $node_id
     * @return float|int
     */
    public function sitecountInclude($siteinfo)
    {
        $site_id = $siteinfo['id'];
        $node_id = $siteinfo['node_id'];
        $count = ArticleSearchengineInclude::where(["node_id" => $node_id, "site_id" => $site_id])->sum("count");
        return $count;
    }
}