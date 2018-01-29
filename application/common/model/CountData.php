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
    //TODO oldfunction
    public function countPv($node_id,$ttime)
    {
        return Pv::where(["node_id"=>$node_id,"create_time"=>["egt",$ttime]])->count();
    }

    /**
     * 获取爬虫信息
     * @param $node_id
     * @param $ttime
     * @return int|string
     */
    //TODO oldfunction
    public function countUseragent($node_id,$ttime)
    {
        return Useragent::where(["node_id"=>$node_id,"create_time"=>["egt",$ttime]])->count();
    }

    /**
     * 获取文章添加数量
     * @param $node_id
     * @param $ttime
     * @return int|string
     */
    //TODO oldfunction
    public function countArticle($node_id,$ttime)
    {
        return Article::where(["node_id"=>$node_id,"create_time"=>["egt",$ttime]])->count();
    }

    /**
     * 获取甩单数量
     * @param $node_id
     * @param $ttime
     * @return int|string
     */
    //TODO oldfunction
    public function countShuaidan($node_id,$ttime)
    {
        return Rejection::where(["node_id"=>$node_id,"create_time"=>["egt",$ttime]])->count();
    }

    /**
     * 收录数量
     * @param $node_id
     * @return float|int
     */
    //TODO oldfunction
    public function countInclude($node_id)
    {
        $count=ArticleSearchengineInclude::where(["node_id"=>$node_id])->sum("count");
        return $count;
    }
}
