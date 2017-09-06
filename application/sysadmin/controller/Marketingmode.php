<?php

namespace app\sysadmin\controller;

use app\common\controller\Common;
use think\Request;
use app\sysadmin\model\Marketingmode as Mark;
use think\Validate;

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
        $where = [];
        if (!empty($content)) {
            $where["content"] = ["like", "%$content%"];
        }
        if (!empty($keyword)) {
            $where["keyword"] = ["like", "%$keyword%"];
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
        $rule = [
            ["title", "require", "请输入标题"],
            ["content", "require", "请输入内容"],
            ["industry_id", "require", "请选择行业分类"],
            ["industry_name","require","请选择行业分类"],
            ["keyword","require","请填写关键词"]
        ];
        $validate = new Validate($rule);
        $data = $request->post();
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), "failed");
        }
        if (!Mark::create($data)) {
            return $this->resultArray("添加失败", "failed");
        }
        return $this->resultArray("添加成功");
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
        $rule = [
            ["title", "require", "请输入标题"],
            ["content", "require", "请输入内容"],
            ["industry_id", "require", "请选择行业分类"],
            ["industry_name","require","请选择行业分类"],
            ["keyword","require","请填写关键词"]
        ];
        $validate = new Validate($rule);
        $data = $request->post();
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), "failed");
        }
        if (!(new Mark)->save($data, ["id" => $id])) {
            return $this->resultArray('修改失败', 'failed');
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
        $Mark = Mark::get($id);
        if (!$Mark->delete()) {
            return $this->resultArray('删除失败', 'failed');
        }
        return $this->resultArray('删除成功');
    }
}
