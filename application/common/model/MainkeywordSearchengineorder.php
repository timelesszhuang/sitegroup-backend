<?php

namespace app\common\model;

use think\Db;
use think\Model;

class  MainkeywordSearchengineorder extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'sc_mainkeyword_searchengineorder';

    protected $connection = 'db2';


    /**
     * 格式化日期
     * @param $value
     * @param $key
     */
    //TODO oldfunction
    public function formatter_date(&$value, $key)
    {
        if ($value['create_time']) {
            $value['create_time'] = date("Y-m-d", $value['create_time']);
        }
    }

    /**
     * 获取所有数据
     * 组织数据
     * @return false|\PDOStatement|string|\think\Collection
     */
    //TODO oldfunction
    public function getType($limit, $rows, $where = 0,$url='')
    {
        $count = $this->where($where)->where('url','like',"%$url%")->count();
        $arr = Db::connect($this->connection)->table("sc_mainkeyword_searchengineorder")->where($where)->where('url|showurl|baiduurl','like',"%$url%")->limit($limit, $rows)->order('mainkeyword_id asc,all_order asc')->select();
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
