<?php

namespace app\common\controller;


use think\Request;

use think\Validate;

class Substitution extends CommonLogin
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $substitution = $this->request->get('front_substitution');
        $request = $this->getLimit();
        $where = [];
        if (!empty($substitution)) {
            $where["front_substitution"] = ["like", "%$substitution%"];
        }
        $user_info = $this->getSessionUserInfo();
        $where["node_id"] =$user_info["node_id"];
        if ($user_info['user_type_name'] == 'site' && $user_info['user_type'] == '3') {
            $where["site_id"] = $user_info["site_id"];
        }
        $data = (new \app\common\model\ArticlekeywordSubstitution())->getAll($request["limit"], $request["rows"], $where);
        return $this->resultArray('','',$data);
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
            ["front_substitution", "require", "请输入替换前的关键词"],
            ["substitution", "require", "请输入替换为的关键词"],
        ];
        $validate = new Validate($rule);
        $data = $request->post();
        $user_info = $this->getSessionUserInfo();
        $data['node_id'] = $user_info["node_id"];
        $data["site_id"] = $user_info["site_id"];
        if (!$validate->check($data)) {
            return $this->resultArray( "failed",$validate->getError());
        }
        if (!\app\common\model\ArticlekeywordSubstitution::create($data)) {
            return $this->resultArray( "failed","添加失败");
        }
        return $this->resultArray("添加成功");
    }

    /**
     * 显示指定的资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function read($id)
    {
        return $this->getread((new \app\common\model\ArticlekeywordSubstitution()), $id);
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
        $rule = [
            ["front_substitution", "require", "请输入替换前的关键词"],
            ["substitution", "require", "请输入替换为的关键词"],
        ];
        $validate = new Validate($rule);
        $data = $request->post();
        if (!$validate->check($data)) {
            return $this->resultArray( "failed",$validate->getError());
        }
        if (!(new \app\common\model\ArticlekeywordSubstitution())->save($data, ["id" => $id])) {
            return $this->resultArray('failed','修改失败');
        }
        return $this->resultArray('修改成功');
    }

    /**
     * 删除指定资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        return $this->deleteRecord((new \app\common\model\ArticlekeywordSubstitution()), $id);
    }




}
