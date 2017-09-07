<?php

namespace app\admin\model;

use think\Model;

class SystemNotice extends Model
{
    /**
     * 获取表格数据
     * @param $limit
     * @param $rows
     * @param int $where
     * @return array
     */
    public function getList($limit, $rows, $node_id,$where = 0)
    {
        $count=  $this->alias("a")->join("system_notice_read b","b.notice_id=a.id and b.node_id=$node_id","LEFT")->field(["a.*,b.id as readid"])->count();
        $data=  $this->alias("a")->join("system_notice_read b","b.notice_id=a.id and b.node_id=$node_id","LEFT")->field(["a.*,b.id as readid"])->limit($limit, $rows)->order('id desc')->select();
        return [
            "total" => $count,
            "rows" => $data
        ];
    }
}
