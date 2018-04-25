<?php
/**
 * Created by PhpStorm.
 * User: qiangbi
 * Date: 17-4-26
 * Time: 下午2:25
 */

namespace app\common\model;

use app\common\traits\Osstrait;
use think\Config;
use think\Model;

class ArticleSyncCount extends Model
{
    use Osstrait;
    //只读字段
    protected $readonly = ["node_id"];

}