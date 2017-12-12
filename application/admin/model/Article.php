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

class Article extends Model
{
    use Osstrait;
    //只读字段
    protected $readonly = ["node_id"];

    /**
     * 初始化函数
     * @author guozhen
     */
    public static function init()
    {
        parent::init();
        $common_func = function ($article) {
            if (isset($article->content)) {
                //提取summary
                //如果前台有传递summary就使用 否则自动提取
                if (!trim($article->summary)) {
                    $article->summary = mb_substr(trim(strip_tags(str_replace('&nbsp;', '', $article->content))), 0, 40 * 3, 'utf-8');
                }
                //如果已经上传了缩略图的话
                $endpoint = Config::get('oss.endpoint');
                $bucket = Config::get('oss.bucket');
                $url = sprintf("https://%s.%s/", $bucket, $endpoint);
                //添加数据之前需要提取下文章中的图片
                // 匹配阿里云网址
                // 如果带着缩略图则 需要生成图片名
                // 如果不带图片名   则需要提取图片
                if ($article->thumbnails) {
                    //有上传缩略图 一定是oss的
                    //拼接缩略图名称
                    //没有图片的
                    if (!array_key_exists('id', $article)) {
                        //添加的时候才需要操作
                        $imgname = md5(uniqid(rand(), true));
                        $imgpathinfo = pathinfo(parse_url($article->thumbnails)['path']);
                        $type = '';
                        if (array_key_exists('extension', $imgpathinfo)) {
                            $type = '.' . $imgpathinfo['extension'];
                        }
                        $article->thumbnails_name = $imgname . $type;
                    }
                } else {
                    //没有上传缩略图
                    //提取文章中
                    preg_match_all('/<img[^>]+src\s*=\\s*[\'\"]([^\'\"]+)[\'\"][^>]*>/i', $article->content, $match);
                    //两种均可
                    if (!empty($match[0])) {
                        if (array_key_exists(1, $match)) {
                            foreach ($match[1] as $k => $v) {
                                if (strpos($v, 'base64') !== false) {
                                    //base64的 不需要提取出来 逃过
                                    continue;
                                }
                                if (strpos($v, $url) === false) {
                                    //表示不是自己的链接 提取第一张图片
                                    $article->thumbnails = $v;
                                    $article->thumbnails_name = '';
                                } else {
                                    $imgname = md5(uniqid(rand(), true));
                                    $imgpathinfo = pathinfo(parse_url($v)['path']);
                                    $type = '';
                                    if (array_key_exists('extension', $imgpathinfo)) {
                                        $type = '.' . $imgpathinfo['extension'];
                                    }
                                    //aliyun oss 的链接
                                    $article->thumbnails_name = $imgname . $type;
                                    $article->thumbnails = $v;
                                }
                                //只需要提取一张图片
                                break;
                            }
                        }
                    }
                }
            }
            //如果阅读数量是空的话
            if (empty($article->readcount)) {
                $article->readcount = rand(100, 10000);
            }
        };
        // 文章阅读数量随机生成 添加图片缩略图
        Article::event("before_insert", $common_func);
        //修改操作
        Article::event("before_update", $common_func);
    }

    /**
     * 获取所有 文章
     * @param $limit
     * @param $rows
     * @param int $where
     * @return array
     */
    public function getArticle($limit, $rows, $where = 0)
    {
        $count = $this->where($where)->count();
        $data = $this->limit($limit, $rows)->where($where)->field('content,summary,update_time,readcount', true)->order('id desc')->select();
        return [
            "total" => $count,
            "rows" => $data
        ];
    }

    /**
     * 获取所有 文章
     * @param $limit
     * @param $rows
     * @param int $where
     * @return array
     */
    public
    function getArticletdk($limit, $rows, $w = '', $wheresite = '', $wheretype_id = '')
    {
        $count = $this->where('articletype_id', 'in', $wheretype_id)->where($w)->whereOr($wheresite)->count();
        $articledata = $this->limit($limit, $rows)->where('articletype_id', 'in', $wheretype_id)->where($w)->whereOr($wheresite)->field('id,title,create_time')->order('id desc')->select();
        return [
            "total" => $count,
            "rows" => $articledata
        ];
    }


}