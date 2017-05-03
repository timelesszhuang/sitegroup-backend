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
    public function getArticletype($limit,$rows,$where=0)
    {
        $count=$this->count();
        $data=$this->limit($limit,$rows)->where($where)->select();
        return [
            "total"=>$count,
            "rows"=>$data
        ];
    }
    public function getArttype($where=0)
    {
        $data =$this->field('id,name')->where($where)->select();
        return $data;
    }





}