<?php
/**
 * Created by PhpStorm.
 * User: jingzheng
 * Date: 2017/4/21
 * Time: 11:35
 */
namespace app\sysadmin\model;

use think\Model;

class Node extends Model{

    /**
     * 分页
     * @param $limit
     * @param $rows
     * @return array
     * @author jingzheng
     */
    public function getNode($limit, $rows)
    {
        $count = $this->count();
        $data = $this->limit($limit, $rows)->order("id", "desc")->select();
        return [
            "total" => $count,
            "rows" => $data
        ];
    }


    public function getUser()
    {
        $data = $this->order("id", "desc")->select();
        return $data;
    }



}