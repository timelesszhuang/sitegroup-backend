<?php

namespace app\common\model;

use app\common\controller\Common;
use think\Model;

class LoginLog extends Model
{

    /**
     * 获取所有 文章
     * @param $limit
     * @param $rows
     * @param int $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getList($limit, $rows, $where = 0)
    {
        $count = $this->where($where)->count();
        $data = $this->limit($limit, $rows)->where($where)->field('update_time',true)->order('id desc')->select();
        return [
            "total" => $count,
            "rows" => $data
        ];
    }

    /**
     * @return array|false|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function lastLoginInfo(){
        $user = (new Common())->getSessionUserInfo();
        $where=[];
        $where['node_id'] = $user['node_id'];
        $where['type'] = $user['user_type'];
        return $this->where($where)->order('id desc')->find();
    }

}
