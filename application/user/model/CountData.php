<?php

namespace app\user\model;

use app\admin\model\Article;
use app\admin\model\Pv;
use app\admin\model\Rejection;
use app\admin\model\Useragent;
use app\common\model\ArticleSearchengineInclude;
use think\Model;

class CountData extends Model
{
    /**
     * 统计浏览量
     */
    public function countPv($siteinfo,$ttime)
    {
        $site_id = $siteinfo['id'];
        $node_id = $siteinfo['node_id'];
        return Pv::where(["node_id"=>$node_id,"site_id"=>$site_id,"create_time"=>["egt",$ttime]])->count();
    }

    /**
     * 获取爬虫信息
     * @param $node_id
     * @param $ttime
     * @return int|string
     */
    public function countUseragent($siteinfo,$ttime)
    {
        $site_id = $siteinfo['id'];
        $node_id = $siteinfo['node_id'];
        return Useragent::where(["node_id"=>$node_id,"site_id"=>$site_id,"create_time"=>["egt",$ttime]])->count();
    }

    /**
     * 获取文章添加数量
     * @param $node_id
     * @param $ttime
     * @return int|string
     */
    public function countArticle($siteinfo,$ttime)
    {
        $site_id = $siteinfo['id'];
        $node_id = $siteinfo['node_id'];
        return Article::where(["node_id"=>$node_id,"site_id"=>$site_id,"create_time"=>["egt",$ttime]])->count();
    }

    /**
     * 获取甩单数量
     * @param $node_id
     * @param $ttime
     * @return int|string
     */
    public function countShuaidan($siteinfo,$ttime)
    {
        $site_id = $siteinfo['id'];
        $node_id = $siteinfo['node_id'];
        return Rejection::where(["node_id"=>$node_id,"site_id"=>$site_id,"create_time"=>["egt",$ttime]])->count();
    }

    /**
     * 收录数量
     * @param $node_id
     * @return float|int
     */
    public function countInclude($siteinfo)
    {
        $site_id = $siteinfo['id'];
        $node_id = $siteinfo['node_id'];
        $count=ArticleSearchengineInclude::where(["node_id"=>$node_id,"site_id"=>$site_id])->sum("count");
        return $count;
    }
}
