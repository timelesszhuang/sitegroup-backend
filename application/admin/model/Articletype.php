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
    public function getArticletype($name="",$id='')
    {
        $where=[];
        if(!empty($name)){
            $where["name"] = ["like", "%$name%"];
        }
        if(!empty($id)){
            $where["id"]=$id;
        }
        $user=(new Common())->getSessionUser();
        $where["node_id"]=$user["user_node_id"];
        $data=$this->where($where)->select();
        return $data;
    }





}