<?php

namespace app\common\controller;

use think\Controller;
use think\Request;
use think\Validate;
use app\common\controller\Common;

class Rejection extends CommonLogin
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
        $detail = $this->request->get('detail');
        $search = $this->request->get('search');
        $where = [];
        if (!empty($site_id)) {
            $where['site_id'] = $site_id;
        }
        if (!empty($detail)) {
            $where['tag_id'] = $detail;
        }
        if (!empty($search)) {
            $where['field1|field2|field3|field4'] = ['like', "%$search%"];
        }
        $user_info = $this->getSessionUserInfo();
        $where["node_id"] =$user_info["node_id"];
        return $this->resultArray('', '', (new \app\common\model\Rejection())->getAll($limits['limit'], $limits['rows'], $where));
    }

    public function getUserDefind()
    {
        $user_info = $this->getSessionUserInfo();
        $where["node_id"] =$user_info["node_id"];
        return $this->resultArray('','',(new \app\common\model\UserDefinedForm())->getForm($where)) ;
    }

}
