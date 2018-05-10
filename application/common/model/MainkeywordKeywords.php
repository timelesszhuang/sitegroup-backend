<?php

namespace app\common\model;

use think\Db;
use think\Model;

class  MainkeywordKeywords extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'sc_mainkeyword_keywords';

    protected $connection = 'db2';


    /**
     * 格式化日期
     * @param $value
     * @param $key
     */
    //TODO oldfunction
    public function formatter_date(&$value,$key)
    {
        if($value['create_time']){
            $value['create_time']=date("Y-m-d",$value['create_time']);
        }
    }
    /**
     * 获取所有关键字分类
     * @return false|\PDOStatement|string|\think\Collection
     */
    //TODO oldfunction
    public function getType($limit, $rows, $where = 0)
    {
        $count = $this->where($where)->count();
        $data=Db::connect($this->connection)->table("sc_mainkeyword_keywords")->where($where)->limit($limit, $rows)->order('count desc')->select();
        array_walk($data,[$this,'formatter_date']);
        return [
            "total" => $count,
            "rows" => $data
        ];
    }



}
