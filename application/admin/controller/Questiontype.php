<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\common\controller\Common;
use think\Validate;

class Questiontype extends Common
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $request=$this->getLimit();
        $name = $this->request->get('name');
        $where=[];
        if(!empty($name)){
            $where["name"] = ["like", "%$name%"];
        }
        $user=(new Common())->getSessionUser();
        $where["node_id"]=$user["user_node_id"];
        $data = (new \app\admin\model\QuestionType())->getAll($request["limit"], $request["rows"], $where);
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
        $rule = [
            ['name', 'require', "请填写分类名称"],
        ];
        $validate = new Validate($rule);
        $data = $request->post();
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), 'faile');
        }
        $data["node_id"] = $this->getSessionUser()['user_node_id'];
        if (!\app\admin\model\QuestionType::create($data)) {
            return $this->resultArray('添加失败', 'faile');
        }
        return $this->resultArray('添加成功');
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        return $this->getread((new \app\admin\model\QuestionType()),$id);
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
        $rule = [
            ['name', 'require', "请填写分类名称"],
        ];
        $validate = new Validate($rule);
        $data = $request->put();
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), 'faile');
        }
        $data["node_id"] = $this->getSessionUser()['user_node_id'];
        if (!\app\admin\model\QuestionType::update($data)) {
            return $this->resultArray('修改失败', 'faile');
        }
        return $this->resultArray('修改成功');
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        if (!\app\admin\model\QuestionType::destroy($id)) {
            return $this->resultArray('删除失败', 'faile');
        }
        return $this->resultArray('删除成功');
    }
}
