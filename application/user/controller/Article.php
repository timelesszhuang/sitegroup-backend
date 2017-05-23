<?php

namespace app\user\controller;

use app\admin\controller\Articletype;
use app\common\controller\Common;
use think\Request;
use think\Session;
use think\Validate;

class Article extends Common
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $request=$this->getLimit();
        $node_id=$this->getSiteSession('login_site');
        $where=[];
        $where["node_id"]=$node_id["node_id"];
        $where["site_id"]=$this->getSiteSession('website')["id"];
        $data = (new \app\admin\model\Article())->getArticle($request["limit"], $request["rows"], $where);
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
            ["articletype_id", "require", "请选择文章分类"],
        ];
        $validate = new Validate($rule);
        $data = $request->post();
        $data['node_id'] =$this->getSiteSession('login_site')["node_id"];
        $data["site_id"] =$this->getSiteSession('website')["id"];
        $data["site_name"] =$this->getSiteSession('website')["site_name"];
        if(!$validate->check($data)) {
            return $this->resultArray($validate->getError(), "failed");
        }
        if (!\app\admin\model\Article::create($data)) {
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
        return $this->getread((new \app\admin\model\Article),$id);
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
            ["articletype_id", "require", "请选择文章分类"],
        ];
        $validate = new Validate($rule);
        $data = $request->post();
        $data['node_id'] =$this->getSiteSession('login_site')["node_id"];
        $data["site_id"] =$this->getSiteSession('website')["id"];
        $data["site_name"] =$this->getSiteSession('website')["site_name"];
        if(!$validate->check($data)) {
            return $this->resultArray($validate->getError(), "failed");
        }
        return $this->publicUpdate((new \app\admin\model\Article),$data,$id);
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        return $this->deleteRecord((new \app\admin\model\Article),$id);
    }

    /**
     * 获取文章类型
     * @return array
     */
    public function getArticleType()
    {
        return (new Articletype)->getType();
    }
}
