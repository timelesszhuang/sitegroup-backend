<?php
/**
 * Created by PhpStorm.
 * User: qiangbi
 * Date: 17-4-26
 * Time: 下午2:25
 */

namespace app\admin\model;
use app\common\controller\Common;
use think\Model;

class Articletype extends Model
{
    //只读字段
    protected $readonly=["node_id"];
    /**
     * @param $limit
     * @param $rows
     * @param int $where
     * @return array
     */
    public function getArticletype($limit,$rows,$where=0)
    {
        $count=$this->where($where)->count();
        $data=$this->limit($limit,$rows)->where($where)->select();
        return [
            "total"=>$count,
            "rows"=>$data
        ];
    }

    /**
     * @param int $where
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getArttype($where=0)
    {
        $data =$this->field('id,name,detail,tag')->where($where)->select();
        return $data;
    }





}