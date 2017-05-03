<?php

namespace app\admin\controller;

use app\common\controller\Common;
use think\Validate;
use think\Request;

class Articletype extends Common
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
        $data = (new \app\admin\model\Articletype())->getArticletype($request["limit"], $request["rows"], $where);
        return $this->resultArray('', '', $data);
    }

    /**
     * @param $id
     * @return array
     */
    public function read($id)
    {
        return $this->resultArray('','',\app\admin\model\Articletype::get($id));
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
        $rule = [
            ["name", "require|unique:Articletype", "请输入文章名|文章名重复"],
            ["detail", "require", "请输入详情"],
        ];
        $validate = new Validate($rule);
        $data = $this->request->post();
        $user = $this->getSessionUser();
        $data['node_id'] = $user['user_node_id'];
        if(!$validate->check($data)) {
            return $this->resultArray($validate->getError(), "failed");
        }
        if (!\app\admin\model\Articletype::create($data)) {
            return $this->resultArray("添加失败", "failed");
        }
        return $this->resultArray("添加成功");
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
        $rule = [
            ["name", "require|unique:Articletype", "请输入文章名|文章名重复"],
            ["detail", "require", "请输入详情"],
        ];
        $data = $this->request->put();
        $validate = new Validate($rule);
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), 'failed');
        }
        if (!\app\admin\model\Articletype::update($data)) {
            return $this->resultArray('修改失败', 'failed');
        }

        return $this->resultArray('修改成功');
    }

    /**
     * 删除指定资源
     * @param  int $id
     * @return \think\Response
     */
    public function delete($id)
    {
//        $Articletype = \app\admin\model\Articletype::get($id);
//        if (!$Articletype->delete()) {
//            return $this->resultArray('删除失败', 'failed');
//        }
//        return $this->resultArray('删除成功');
    }

    /**
     * @return array
     *
     */
    public function getType(){
        $where=[];
        $user=(new Common())->getSessionUser();
        $where["node_id"]=$user["user_node_id"];
        $data = (new \app\admin\model\Articletype())->getArttype($where);
        return $this->resultArray('', '', $data);
    }
}
