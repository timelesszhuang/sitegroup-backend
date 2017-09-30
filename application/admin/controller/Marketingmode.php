<?php

namespace app\admin\controller;

use app\common\controller\Common;
use think\Request;
use app\admin\model\Marketingmode as Mark;
class Marketingmode extends Common
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $request = $this->getLimit();
        $content = $this->request->get('content');
        $keyword = $this->request->get('keyword');
        $industry_id = $this->request->get('industry_id');

        $where = [];
        if (!empty($content)) {
            $where["title|content|summary"] = ["like", "%$content%"];
        }
        if (!empty($keyword)) {
            $where["keyword"] = ["like", "%$keyword%"];
        }
        if(!empty($industry_id)){
            $where["industry_id"]=$industry_id;
        }
        $data = (new Mark())->getList($request["limit"], $request["rows"], $where);
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
        $mark=Mark::get($id);
        $mark->readcount++;
        $mark->save();
        return $this->resultArray('','',$mark);
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

    /**
     * 获取前4条数据给前台
     * @return array
     */
    public function getFour()
    {
        $data = (new Mark())->limit(4)->order("id","desc")->field("id,img")->select();
        return $this->resultArray('', '', $data);
    }
}
