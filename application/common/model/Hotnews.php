<?php

namespace app\common\model;

use think\Db;
use think\Model;

class Hotnews extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'sc_hotnews';

    protected $connection = 'db2';


    /**
     * 格式化日期
     * @param $value
     */
    public function formatter_date(&$value)
    {
        if ($value['create_time']) {
            $value['create_time'] = date("Y-m-d H:i:s", $value['create_time']);
        }
    }

    /**
     * 获取所有关键字分类
     * @param $limit
     * @param $rows
     * @param int $where
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getHot($limit, $rows, $where = 0)
    {
        $count = $this->where($where)->count();
        $data = Db::connect($this->connection)->table("sc_hotnews")->field(["base64img","create_time","id","title","summary"])->where($where)->order('id desc')->limit($limit, $rows)->select();
        /** @var array $data */
        array_walk($data, [$this, 'formatter_date']);
        return [
            "total" => $count,
            "rows" => $data
        ];
    }


    /**
     * 获取一条数据
     * @param $id
     * @return null|static
     * @throws \think\exception\DbException
     */
    public function getOne($id)
    {
        $key = self::get($id);
        return $key;
    }

    /**
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getKeyTypeList()
    {
        return Db::connect($this->connection)->table("sc_weixin_keyword_type")->field("id,name as text")->select();
    }

}
