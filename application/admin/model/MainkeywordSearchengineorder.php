<?php

namespace app\admin\model;

use think\Db;
use think\Model;

class  MainkeywordSearchengineorder extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'sc_mainkeyword_searchengineorder';

    protected $connection = [
        // 数据库类型
        'type' => 'mysql',
        // 数据库连接DSN配置
        'dsn' => '',
        // 服务器地址
        'hostname' => 'rdsfjnifbfjnifbo.mysql.rds.aliyuncs.com',
//        // 数据库名
        'database' => 'scrapy',
        // 数据库用户名
        'username' => 'scrapy',
        // 数据库密码
        'password' => '201671Zhuang',
        // 数据库连接端口
        'hostport' => '',
        // 数据库连接参数
        'params' => [],
        // 数据库编码默认采用utf8
        'charset' => 'utf8',
        // 数据库表前缀
        'prefix' => 'sc_',
    ];


    /**
     * 格式化日期
     * @param $value
     * @param $key
     */
    public function formatter_date(&$value, $key)
    {
        if ($value['create_time']) {
            $value['create_time'] = date("Y-m-d H:i:s", $value['create_time']);
        }
    }

    /**
     * 获取所有数据
     * 组织数据
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getType($limit, $rows, $where = 0,$url='')
    {
        $count = $this->where($where)->where('url','like',"%$url%")->count();
        $arr = Db::connect($this->connection)->table("sc_mainkeyword_searchengineorder")->where($where)->where('url|showurl|baiduurl','like',"%$url%")->limit($limit, $rows)->order('all_order asc,mainkeyword_id asc')->select();
        $data = [];
        foreach ($arr as $k => $v) {
            if ($v['url']) {
                $v['a_href'] = $v['url'];
                $v['a_text'] = $v['url'];
            } else if (empty($v['url']) || !empty($v['show_url'])) {
                $v['a_href'] = $v['baiduurl'];
                $v['a_text'] = $v['showurl'];
            } else if (empty($v['show_url']) || !empty($v['baiduurl'])) {
                $v['a_href'] = $v['baiduurl'];
                $v['a_text'] = '未获取到网址，点击跳转';
            } else {
                $v['a_text'] = "未获取到链接";
            }
            if (empty($v['keywords'])) {
                $v['keywords'] = '';
            }
            if (empty($v['description'])) {
                $v['description'] = "";
            }
            $data[$k] = $v;
        }
        array_walk($data, [$this, 'formatter_date']);
        return [
            "total" => $count,
            "rows" => $data
        ];
    }


}
