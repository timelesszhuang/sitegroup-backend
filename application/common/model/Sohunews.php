<?php

namespace app\common\model;

use think\Model;
use think\Db;

class Sohunews extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'sc_sohunews';

    protected $connection = 'db2';

    /**
     * 获取所有关键字
     * @param $limit
     * @param $rows
     * @param int $where
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getArticle($limit, $rows, $where = 0)
    {
        $count = $this->where($where)->count();
        $data = Db::connect($this->connection)->table($this->table)->where($where)->order('id desc')->field('content', true)->limit($limit, $rows)->select();
        /** @var array $data */
        array_walk($data, [$this, 'formatter_date']);
        return [
            "total" => $count,
            "rows" => $data
        ];
    }

    /**
     * 格式化日期
     * @param $value
     */
    public function formatter_date(&$value)
    {
        if (isset($value['create_time'])) {
            $value['create_time'] = date("Y-m-d H:i:s", $value['create_time']);
        }
    }


    /**
     * 获取单篇文章
     * @param $id
     * @return null|static
     * @throws \think\exception\DbException
     */
    public function getOne($id)
    {
        $key = self::get($id);
        return $key;
    }
}
