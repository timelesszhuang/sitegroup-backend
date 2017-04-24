<?php
/**
 * Created by PhpStorm.
 * User: jingzheng
 * Date: 2017/4/21
 * Time: 11:35
 */
namespace app\common\model;

use think\Model;

class Node extends Model{
    /**
     * 分页
     * @param $limit
     * @param $rows
     * @return array
     * @auther jingzheng
     */
    public function getNode($limit, $rows)
    {
        $count = $this->count();
        $data = $this->limit($limit, $rows)->order("id", "desc")->field("id,name,detail,com_id,com_name,user_id,create_time,update_time")->select();
        return [
            "total" => $count,
            "rows" => $data
        ];
    }



}