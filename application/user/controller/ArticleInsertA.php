<?php

namespace app\user\controller;


use think\Request;
use app\common\controller\Common;
use think\Session;
use think\Validate;

class ArticleInsertA extends Common
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $href = $this->request->get('href');
        $request = $this->getLimit();
        $node_id = $this->getSiteSession('login_site');
        $where = [];
        if (!empty($href)) {
            $where["href"] = ["like", "%$href%"];
        }
        $where["node_id"] = $node_id["node_id"];
        $where["site_id"] = $this->getSiteSession('website')["id"];
        $data = (new \app\admin\model\ArticleInsertA())->getAll($request["limit"], $request["rows"], $where);
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
            ["title", "require", "请输入title"],
            ["content", "require", "请输入内容"],
            ["href", "require", "请输入链接"],
        ];
        $validate = new Validate($rule);
        $data = $request->post();
        $user = $this->getSessionUser();
        $data['node_id'] = $this->getSiteSession('login_site')["node_id"];
        $data["site_id"] = $this->getSiteSession('website')["id"];
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), "failed");
        }
        if (!\app\admin\model\ArticleInsertA::create($data)) {
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
        return $this->getread((new \app\admin\model\ArticleInsertA()), $id);
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
            ["title", "require", "请输入title"],
            ["content", "require", "请输入内容"],
            ["href", "require", "请输入链接"],
        ];
        $validate = new Validate($rule);
        $data = $request->post();
        $data['node_id'] = $this->getSiteSession('login_site')["node_id"];
        $data["site_id"] = $this->getSiteSession('website')["id"];
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), "failed");
        }
        if (!(new \app\admin\model\ArticleInsertA())->save($data, ["id" => $id])) {
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
        return $this->deleteRecord((new \app\admin\model\ArticleInsertA()), $id);
    }

    /**
     * 小网站用户存储站点信息
     * @return array
     */
    public function siteInfo()
    {

    }

    /**
     * 设置session 全部都放进去 以后有用
     * @param $site_id
     * @param $site_name
     */
    public function setSession($site_info)
    {

    }
}
