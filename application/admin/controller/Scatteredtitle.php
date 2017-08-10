<?php

namespace app\admin\controller;

use app\admin\model\Scatteredarticletype;
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
        $article_type = $request->get("article_type");
        $where = [];
        if (!empty($title)) {
            $where['title'] = ["like", "%$title%"];
        }
        if (!empty($article_type)) {
            $where['articletype_id'] = $article_type;
        }
        $user = $this->getSessionUser();
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
     * @param  \think\Request $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $rule = [
            ['title', 'require', "请填写标题"],
            ["articletype_id", "require", "请选择分类id"],
            ["articletype_name", "require", "请选择分类名称"]
        ];
        $validate = new Validate($rule);
        $data = $this->request->post();
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), 'failed');
        }
        if(\app\admin\model\ScatteredArticle::where(["articletype_id"=>$data['articletype_id']])->count()<15){
            return $this->resultArray("当前文章段落文章少于15篇,请先补充文章", 'failed');
        }
        $data["node_id"] = $this->getSessionUser()['user_node_id'];
        if (!\app\admin\model\ScatteredTitle::create($data)) {
            return $this->resultArray('添加失败', 'failed');
        }
        return $this->resultArray('添加成功');
    }

    /**
     * 显示指定的资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function read($id)
    {
        return $this->getread((new \app\admin\model\ScatteredTitle), $id);
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int $id
     * @return \think\Response
     */
    public function edit($id)
    {

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
            ['title', 'require', "请填写标题"],
            ["articletype_id", "require", "请选择分类id"],
            ["articletype_name", "require", "请选择分类名称"]
        ];
        $validate = new Validate($rule);
        $data = $this->request->put();
        if(\app\admin\model\ScatteredArticle::where(["articletype_id"=>$data['articletype_id']])->count()<15){
            return $this->resultArray("当前文章段落文章少于15篇,请先补充文章", 'failed');
        }
        $data["update_time"]=time();
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), 'failed');
        }
        if (!(new \app\admin\model\ScatteredTitle)->save($data, ["id" => $id])) {
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
        return $this->deleteRecord((new \app\admin\model\ScatteredTitle),$id);
    }

    /**
     *根据id获取标题和article
     * @return array
     */
    public function getArrticleJoinTitle($id)
    {
        $title = (new \app\admin\model\ScatteredTitle)->where(["id" => $id])->find();
        $article = '未设置文章';
        $data = '';
        if ($title["article_ids"]) {
            $data = \app\admin\model\ScatteredArticle::all($title["article_ids"]);
            $data = collection($data)->toArray();
            $data = array_column($data, "content_paragraph");
            $article = implode("<br/>", $data);
        }
        $title['content'] = $article;
        return $this->resultArray('', '', $title);
    }



}
