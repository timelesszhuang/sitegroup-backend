<?php

namespace app\common\model;

use think\Model;
use think\Db;

class WangyiArticle extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'sc_163news';

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
        if ($value['createtime']) {
            $value['createtime'] = date("Y-m-d H:i:s", $value['createtime']);
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

    /**
     * 修改文章
     * @param $id
     * @param $title
     * @param $content
     * @param string $digest
     * @param string $source
     * @return int|string
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function editKeyword($id, $title, $content, $digest = '', $source = '')
    {
        $update = [
            "title" => $title,
            "content" => $content
        ];
        if (!empty($digest)) {
            $update["digest"] = $digest;
        }
        if (!empty($source)) {
            $update["source"] = $source;
        }
        return Db::connect($this->connection)->table("sc_163news")->where(["id" => $id])->update($update);
    }

    /**
     * 删除文章
     * @param $id
     * @return int
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function deleteOne($id)
    {
        return Db::connect($this->connection)->table($this->table)->delete($id);
    }

    /**
     * 获取所有分类
     */
    public function allTypes()
    {
        $arr = [
            ["id" => 1, "text" => "科技"],
            ["id" => 2, "text" => "教育"],
            ["id" => 3, 'text' => "财经"]
        ];
        return $arr;
    }
}
