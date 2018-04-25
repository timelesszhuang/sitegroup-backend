<?php
/**
 * Created by PhpStorm.
 * User: qiangbi
 * Date: 17-6-14
 * Time: 上午8:58
 */

namespace app\common\controller;

use app\common\model\ArticleSyncCount;
use app\common\model\SiteStaticconfig;

class CrontabTask extends Common
{

    /**
     * 执行定时一键更新网站
     */
    public function index()
    {
        $Menu = (new \app\common\model\Menu());
        $ArticleSyncCount = (new ArticleSyncCount());
        $SiteStaticconfig = (new SiteStaticconfig());
        $Article = (new \app\common\model\Article());
        $Product = (new \app\common\model\Product());
        $Question = (new \app\common\model\Question());
        $site = (new \app\common\model\Site());
        //获取整站所有的网站列表
        $sites = $site->where(['id' => 83])->field("id,url,menu,site_name,node_id")->select();
        $time = time();
        foreach ($sites as $item) {
            /*以下操作针对单一网站*/
            //初始化静态化篇数
            $a_limit = 5;
            $p_limit = 5;
            $q_limit = 5;
            //获取文章同步详情
            $article_count_info = $ArticleSyncCount->where(['site_id' => $item['id'], 'type_name' => 'article'])->field('id,count,laststatic_time')->find();
            //获取文章同步配置列表
            $article_static_config = $SiteStaticconfig->where(['site_id' => $item['id'], 'type' => 'article'])->select();
            //获取产品同步详情
            $product_count_info = $ArticleSyncCount->where(['site_id' => $item['id'], 'type_name' => 'product'])->field('id,count,laststatic_time')->find();
            //获取产品同步配置列表
            $product_static_config = $SiteStaticconfig->where(['site_id' => $item['id'], 'type' => 'product'])->select();
            //获取问答同步详情
            $question_count_info = $ArticleSyncCount->where(['site_id' => $item['id'], 'type_name' => 'question'])->field('id,count,laststatic_time')->find();
            //获取问答同步配置列表
            $question_static_config = $SiteStaticconfig->where(['site_id' => $item['id'], 'type' => 'question'])->select();
            //配置文章更新个数
            if (!$article_static_config) {
                //当没有同步配置时
                //当有同步记录且同步记录不在今天
                if ($article_count_info && $article_count_info['laststatic_time'] >= strtotime(date('Y-m-d', $time))) {
                    $a_limit = 0;
                }
            } else {
                $a_limit = 0;
                foreach ($article_static_config as $static_config) {
                    //没有同步记录或者同步记录数据最后同步时间小于当前配置的开始时间时符合时间范围的时候
                    if (((!$article_count_info) || ($article_count_info['laststatic_time'] <= strtotime(date("Y-m-d " . $static_config['starttime'], $time))))
                        && ($time > strtotime(date("Y-m-d " . $static_config['starttime'], $time))
                            && $time <= strtotime(date("Y-m-d " . $static_config['stoptime'], $time)))) {
                        $a_limit = $static_config['staticcount'];
                    }
                }
            }
            if (!$product_static_config) {
                //当没有同步配置时
                //当有同步记录且同步记录不在今天
                if ($product_count_info && $product_count_info['laststatic_time'] >= strtotime(date('Y-m-d', $time))) {
                    $p_limit = 0;
                }
            } else {
                $p_limit = 0;
                foreach ($product_static_config as $static_config) {
                    //没有同步记录或者同步记录数据最后同步时间小于当前配置的开始时间时符合时间范围的时候
                    if (((!$product_count_info) || ($product_count_info['laststatic_time'] <= strtotime(date("Y-m-d " . $static_config['starttime'], $time))))
                        && ($time > strtotime(date("Y-m-d " . $static_config['starttime'], $time))
                            && $time <= strtotime(date("Y-m-d " . $static_config['stoptime'], $time)))) {
                        $p_limit = $static_config['staticcount'];
                    }
                }
            }
            if (!$question_static_config) {
                //当没有同步配置时
                //当有同步记录且同步记录不在今天
                if ($question_count_info && $question_count_info['laststatic_time'] >= strtotime(date('Y-m-d', $time))) {
                    $q_limit = 0;
                }
            } else {
                $q_limit = 0;
                foreach ($question_static_config as $static_config) {
                    //没有同步记录或者同步记录数据最后同步时间小于当前配置的开始时间时符合时间范围的时候
                    if (((!$question_count_info)
                            || ($question_count_info['laststatic_time'] <= strtotime(date("Y-m-d " . $static_config['starttime'], $time))))
                        && ($time > strtotime(date("Y-m-d " . $static_config['starttime'], $time))
                            && $time <= strtotime(date("Y-m-d " . $static_config['stoptime'], $time)))) {
                        $q_limit = $static_config['staticcount'];
                    }
                }
            }
            $data['node_id'] = $item['node_id'];
            $data['site_id'] = $item['id'];
            $data['site_name'] = $item['site_name'];
            //根据menu列表中的type_id分别获取文章
            $menu_id_arr = array_unique(array_filter(explode(',', $item['menu'])));

            if ($a_limit > 0) {
                //获取文章类型的栏目列表
                $article_menu_list = $Menu->where(['flag' => 3, 'id' => ['in', $menu_id_arr]])->field("id,type_id")->select();
                $article_type_id_arr = [];
                foreach ($article_menu_list as $menu_info) {
                    //获取文章类型id列表
                    $article_type_id_arr = array_unique(array_merge($article_type_id_arr, array_unique(array_filter(explode(',', $menu_info['type_id'])))));
                }
                $dates = [];
                //组织添加或更新数据
                if ($article_count_info) {
                    $dates['id'] = $article_count_info['id'];
                    $article_count = $article_count_info['count'];
                    $is_update = true;
                } else {
                    $dates = $data;
                    $dates['type_name'] = 'article';
                    $article_count = 0;
                    $is_update = false;
                }
                $now_article_count = $Article->where(['articletype_id' => ['in', $article_type_id_arr], 'id' => ['>', $article_count]])->limit($a_limit)->order('id asc')->field('id')->max('id');
                //根据情况判断同步数量
                if ($now_article_count) {
                    $dates['count'] = $now_article_count;
                } else {
                    $dates['count'] = $article_count;
                }
                $dates['laststatic_time'] = $time;
                //根据情况添加或更新记录
                if ($is_update) {
                    $ArticleSyncCount->update($dates);
                } else {
                    $ArticleSyncCount->insert($dates);
                }
            }
            //同文章
            if ($p_limit > 0) {
                $product_menu_list = $Menu->where(['flag' => 5, 'id' => ['in', $menu_id_arr]])->field("id,type_id")->select();
                $product_type_id_arr = [];
                foreach ($product_menu_list as $menu_info) {
                    $product_type_id_arr = array_unique(array_merge($product_type_id_arr, array_unique(array_filter(explode(',', $menu_info['type_id'])))));
                }
                $dates = [];
                if ($product_count_info) {
                    $dates['id'] = $product_count_info;
                    $product_count = $product_count_info['count'];
                    $is_update = true;
                } else {
                    $dates = $data;
                    $dates['type_name'] = 'product';
                    $product_count = 0;
                    $is_update = false;
                }
                $now_product_count = $Product->where(['type_id' => ['in', $product_type_id_arr], 'id' => ['>', $product_count]])->limit($p_limit)->order('id asc')->field('id')->max('id');
                if ($now_product_count) {
                    $dates['count'] = $now_product_count;
                } else {
                    $dates['count'] = $product_count;
                }
                $dates['laststatic_time'] = $time;
                if ($is_update) {
                    $ArticleSyncCount->update($dates);
                } else {
                    $ArticleSyncCount->insert($dates);
                }
            }
            //同文章
            if ($q_limit > 0) {
                $question_menu_list = $Menu->where(['flag' => 2, 'id' => ['in', $menu_id_arr]])->field("id,type_id")->select();
                $question_type_id_arr = [];
                foreach ($question_menu_list as $menu_info) {
                    $question_type_id_arr = array_unique(array_merge($question_type_id_arr, array_unique(array_filter(explode(',', $menu_info['type_id'])))));
                }
                $dates = [];
                if ($question_count_info) {
                    $dates['id'] = $question_count_info;
                    $question_count = $question_count_info['count'];
                    $is_update = true;
                } else {
                    $dates = $data;
                    $dates['type_name'] = 'question';
                    $question_count = 0;
                    $is_update = false;
                }
                $now_question_count = $Question->where(['type_id' => ['in', $question_type_id_arr], 'id' => ['>', $question_count]])->limit($q_limit)->order('id asc')->field('id')->max('id');
                if ($now_question_count) {
                    $dates['count'] = $now_question_count;
                } else {
                    $dates['count'] = $question_count;
                }
                $dates['laststatic_time'] = $time;
                if ($is_update) {
                    $ArticleSyncCount->update($dates);
                } else {
                    $ArticleSyncCount->insert($dates);
                }
            }
            //清除当前网站缓存
            pclose(popen("curl $item->url/clearCache &", "r"));
        }
    }
}