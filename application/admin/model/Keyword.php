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

class Keyword extends Model
{
    //只读字段
    protected $readonly=["node_id"];
    /**
     * 根据tag获取数据
     * @param $tag
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getKeyword($tag="",$id=0)
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
        $data=$this->where($where)->field("id,name as label,tag")->select();
        return $data;
    }
}