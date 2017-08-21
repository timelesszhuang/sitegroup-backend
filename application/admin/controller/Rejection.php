<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Validate;
use app\common\controller\Common;

class Rejection extends Common
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $limits = $this->getLimit();
        $site_id = $this->request->get('site_id');
        $where = [];
        if (!empty($site_id)) {
            $where['site_id'] = $site_id;
        }
        $user = $this->getSessionUser();
        $where["node_id"] = $user["user_node_id"];
        return $this->resultArray('', '', (new \app\admin\model\Rejection())->getAll($limits['limit'], $limits['rows'], $where));
    }

}
