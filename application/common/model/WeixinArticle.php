<?php

namespace app\common\model;

use think\Db;
use think\Model;

class WeixinArticle extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'sc_weixin_keywordarticle';

    protected $connection = [
        // 数据库类型
        'type'        => 'mysql',
        // 数据库连接DSN配置
        'dsn'         => '',
        // 服务器地址
        'hostname'    => 'rdsfjnifbfjnifbo.mysql.rds.aliyuncs.com',
//        // 数据库名
        'database'    => 'scrapy',
        // 数据库用户名
        'username'    => 'scrapy',
        // 数据库密码
        'password'    => '201671Zhuang',
        // 数据库连接端口
        'hostport'    => '',
        // 数据库连接参数
        'params'      => [],
        // 数据库编码默认采用utf8
        'charset'     => 'utf8',
        // 数据库表前缀
        'prefix'      => 'sc_',
    ];

    /**
     * 获取所有关键字
     * @return false|\PDOStatement|string|\think\Collection
     */
    //TODO oldfunction
    public function getArticle($limit, $rows, $where = 0)
    {
        $count = $this->where($where)->count();
        $data=Db::connect($this->connection)->table("sc_weixin_keywordarticle")->where($where)->order('id desc')->field('content',true)->limit($limit, $rows)->select();
        array_walk($data,[$this,'formatter_date']);
        return [
            "total" => $count,
            "rows" => $data
        ];
    }

    /**
     * 格式化日期
     * @param $value
     * @param $key
     */
    //TODO oldfunction
    public function formatter_date(&$value,$key)
    {
        if($value['scrapytime']){
            $value['scrapytime']=date("Y-m-d H:i:s",$value['scrapytime']);
        }
    }

    /**
     * 获取单篇文章
     * @param $id
     * @return null|static
     */
    //TODO oldfunction
    public function getOne($id)
    {
        $key=self::get($id);
        return $key;
    }

    /**
     * 修改文章
     * @param $id
     * @param $title
     * @param $content
     * @return int|string
     */
    //TODO oldfunction
    public function editKeyword($id,$title,$content,$summary='',$source='')
    {
        $update=[
            "title"=>$title,
            "content"=>$content
        ];
        if(!empty($summary)){
            $update["summary"]=$summary;
        }
        if(!empty($source)){
            $update["source"]=$source;
        }
        return Db::connect($this->connection)->table("sc_weixin_keywordarticle")->where(["id"=>$id])->update($update);
    }

    /**
     * 删除文章
     * @param $id
     */
    //TODO oldfunction
    public function deleteOne($id)
    {
       return  Db::connect($this->connection)->table("sc_weixin_keywordarticle")->delete($id);
    }

}