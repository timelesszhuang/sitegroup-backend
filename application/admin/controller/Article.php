<?php

namespace app\admin\controller;

use app\common\controller\Common;
use think\Validate;
use think\Request;

class Article extends Common
{
    /**
     * @return array
     */
    public function index()
    {
        $request=$this->getLimit();
        $name = $this->request->get('name');
        $id = $this->request->get('id');
        $where=[];
        if(!empty($name)){
            $where["name"] = ["like", "%$name%"];
        }
        if(!empty($id)){
            $where["id"]=$id;
        }
        $user=(new Common())->getSessionUser();
        $where["node_id"]=$user["user_node_id"];
        $data = (new \app\admin\model\Article())->getArticle($request["limit"], $request["rows"], $where);
        return $this->resultArray('', '', $data);
    }

    /**
     * @param $id
     * @return array
     */
    public function read($id)
    {

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

    }

    /**
     * 删除指定资源
     * @param  int $id
     * @return \think\Response
     */
    public function delete($id)
    {

    }
}
