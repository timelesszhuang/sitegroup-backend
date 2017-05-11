<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\common\controller\Common;
use think\Validate;

class Scatteredtitle extends Common
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index(Request $request)
    {
        $limits = $this->getLimit();
        $title = $request->get('title');
        $article_type=$request->get("article_type");
        $where = [];
        if (!empty($title) && !empty($article_type)) {
            $where['title'] = ["like", "%$title%"];
        }
        $user = (new Common)->getSessionUser();
        $where["node_id"] = $user["user_node_id"];
        return $this->resultArray('', '', (new \app\admin\model\ScatteredTitle())->getAll($limits['limit'], $limits['rows'], $where));
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
            ['title', 'require', "请填写标题"],
            ["articletype_id","require","请选择分类id"],
            ["articletype_name","require","请选择分类名称"]
        ];
        $validate = new Validate($rule);
        $data = $this->request->post();
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), 'faile');
        }
        $data["node_id"] = $this->getSessionUser()['user_node_id'];
        if (!\app\admin\model\ScatteredTitle::create($data)) {
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
        return $this->getread((new \app\admin\model\ScatteredTitle),$id);
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {

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
            ['title', 'require', "请填写标题"],
            ["articletype_id","require","请选择分类id"],
            ["articletype_name","require","请选择分类名称"]
        ];
        $validate = new Validate($rule);
        $data = $this->request->put();
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), 'faile');
        }
        $data["node_id"] = $this->getSessionUser()['user_node_id'];
        if (!\app\admin\model\ScatteredTitle::update($data)) {
            return $this->resultArray('添加失败', 'faile');
        }
        return $this->resultArray('添加成功');
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        if (!\app\admin\model\ScatteredTitle::destroy($id)) {
            return $this->resultArray('删除失败', 'faile');
        }
        return $this->resultArray('删除成功');
    }

    /**
     *根据id获取标题和article
     * @return array
     */
    public function getArrticleJoinTitle($id)
    {
        $title=(new \app\admin\model\ScatteredTitle)->where(["id"=>$id])->find();
        $data='';
        if($title["article_ids"]){
            $data=\app\admin\model\ScatteredArticle::all($title["article_ids"]);
            $data=collection($data)->toArray();
        }
        return $this->resultArray('','',["title"=>$title,"article"=>$data]);
    }
}
