<?php

namespace app\admin\controller;

use app\user\controller\PageInfo;
use app\user\model\SitePageinfo;
use think\Request;
use app\common\controller\Common;
use think\Validate;

class Tdk extends Common
{
    /**
     * 大后台统一修改站点tdk操作
     * @author guozhen
     * @param $id
     * @return \think\Response
     */
    public function save($id)
    {
        return (new PageInfo)->update($id);
    }

    /**
     * 查取对应site_id的网站
     * @param $id
     * @return array
     */
    public function search($id)
    {
        $request=$this->getLimit();
        $user = $this->getSessionUser();
        $where["node_id"] = $user["user_node_id"];
        $where["site_id"]=$id;
        $data = (new SitePageinfo)->getAll($request["limit"], $request["rows"], $where);
        return $this->resultArray('', '', $data);
    }
}
