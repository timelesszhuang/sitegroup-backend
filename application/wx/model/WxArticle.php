<?php
/**
 * Created by PhpStorm.
 * User: qiangbi
 * Date: 17-4-26
 * Time: 下午2:25
 */

namespace app\wx\model;

use app\common\traits\Osstrait;
use think\Config;
use think\Model;

class WxArticle extends Model
{
    use Osstrait;

    public function getAll($limit,$rows,$where)
    {
        $count = $this->where($where)->count();
        $data = $this->limit($limit, $rows)->where($where)->order('id desc')->select();
        return [
            "total" => $count,
            "rows" => $data
        ];
    }
}