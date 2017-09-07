<?php

namespace app\admin\controller;

use app\common\controller\Common;
use think\Request;
use app\admin\model\CaseCenter as CaseC;
use think\Validate;

class CaseCenter extends Common
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $request = $this->getLimit();
        $title = $this->request->get('title');
        $keyword = $this->request->get('keyword');
        $industry_id = $this->request->get("industry_id");
        $where = [];
        if (!empty($title)) {
            $where["title"] = ["like", "%$title%"];
        }
        if (!empty($keyword)) {
            $where["keyword"] = ["like", "%$keyword%"];
        }
        if (!empty($industry_id)) {
            $where['industry_id'] = $industry_id;
        }
        $data = (new CaseC())->getList($request["limit"], $request["rows"], $where);
        return $this->resultArray('', '', $data);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        $CaCenter=CaseC::get($id);
        return $this->resultArray('','',$CaCenter);
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}
