<?php
/**
 * Created by PhpStorm.
 * User: qiangbi
 * Date: 17-4-26
 * Time: 下午2:25
 */

namespace app\admin\model;

use app\common\model\Common;
use think\Model;
use think\Session;

class Keyword extends Model
{
    /**
     * 根据tag获取数据
     * @param $tag
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getKeyword($tag=null,$id=0)
    {
        $where=[];
        if(!empty($tag)){
            $where["tag"]=$tag;
        }
        if(!empty($id)){
            $where["parent_id"]=$id;
        }
        $user=(new Common)->getSessionUser();
        $where["node_id"]=$user["user_node_id"];
        $data=$this->where($where)->select();
        return $data;
    }




}