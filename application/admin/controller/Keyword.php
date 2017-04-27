<?php

namespace app\admin\controller;

use app\common\controller\Common;
use think\Request;
use think\worker\Server;

class Keyword extends Common
{
    /**
     * 显示资源列表
     * @return \think\Response
     * @auther jingzheng
     */
    public function index()
    {
        $tag = $this->request->get('tag');
        if ($tag == "A") {
            $tag = "B";
        } else if ($tag == "B") {
            $tag = "C";
        }
        if (empty($tag)) {
            $tag = "A";
        }
        $id = $this->request->get('parent_id');
        $data = (new \app\admin\model\Keyword())->getKeyword($tag, $id);
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
     * @param  \think\Request $request
     * @return \think\Response
     */
    public function save(Request $request)
    {

    }

    /**
     * 显示指定的资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function read($id)
    {
        return $this->resultArray('', '', \app\admin\model\Keyword::get($id));
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request $request
     * @param  int $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     * @param  int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }

    public function uploadA()
    {
        $this->request->server();
    }
}
