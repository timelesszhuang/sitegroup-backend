<?php

namespace app\user\controller;


use think\Request;
use app\common\controller\Common;
use think\Session;
use think\Validate;

class Substitution extends Common
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
        $node_id = $this->getSiteSession('login_site');
        $where = [];
        if (!empty($substitution)) {
            $where["front_substitution"] = ["like", "%$substitution%"];
        }
        $where["node_id"] = $node_id["node_id"];
        $where["site_id"] = $this->getSiteSession('website')["id"];
        $data = (new \app\admin\model\ArticlekeywordSubstitution())->getAll($request["limit"], $request["rows"], $where);
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
        $user = $this->getSessionUser();
        $data['node_id'] = $this->getSiteSession('login_site')["node_id"];
        $data["site_id"] = $this->getSiteSession('website')["id"];
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), "failed");
        }
        if (!\app\admin\model\ArticlekeywordSubstitution::create($data)) {
            return $this->resultArray("添加失败", "failed");
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
        return $this->getread((new \app\admin\model\ArticlekeywordSubstitution()), $id);
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
        $data['node_id'] = $this->getSiteSession('login_site')["node_id"];
        $data["site_id"] = $this->getSiteSession('website')["id"];
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), "failed");
        }
        if (!(new \app\admin\model\ArticlekeywordSubstitution())->save($data, ["id" => $id])) {
            return $this->resultArray('修改失败', 'failed');
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
        return $this->deleteRecord((new \app\admin\model\ArticlekeywordSubstitution()), $id);
    }

    /**
     * 小网站用户存储站点信息
     * @return array
     */
    public function siteInfo()
    {

    }



}
