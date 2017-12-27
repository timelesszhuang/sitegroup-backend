<?php

namespace app\admin\controller;

use app\common\controller\Common;
use think\Db;
use think\Validate;
use think\Request;
use app\admin\model\TypeTag as Type_Tag;

class Articletype extends Common
{
    /**
     * @return array
     */
    public function index()
    {
        $request = $this->getLimit();
        $name = $this->request->get('name');
        $id = $this->request->get('id');
        $where = [];
        if (!empty($name)) {
            $where["name"] = ["like", "%$name%"];
        }
        if (!empty($id)) {
            $where["id"] = $id;
        }
        $user = $this->getSessionUser();
        $where["node_id"] = $user["user_node_id"];
        $data = (new \app\admin\model\Articletype())->getArticletype($request["limit"], $request["rows"], $where);
        return $this->resultArray('', '', $data);
    }

    /**
     * @param $id
     * @return array
     */
    public function read($id)
    {
        return $this->resultArray('', '', \app\admin\model\Articletype::get($id));
    }

    /**
     * 显示创建资源表单页.
     *
     * @return void
     */
    public function create()
    {
        //
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request $request
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function save(Request $request)
    {
        $rule = [
            ["name", "require", "请输入文章分类名"],
            ["detail", "require", "请输入详情"],
            ["alias", "require", "请输入此分类的英文名"]
        ];
        $validate = new Validate($rule);
        $data = $this->request->post();
        $user = $this->getSessionUser();
        $data['node_id'] = $user['user_node_id'];
        $where['node_id'] = $user['user_node_id'];
        $where['alias'] = $data['alias'];
        $typedata = (new \app\admin\model\Articletype())->where($where)->select();
        if ($typedata) {
            return $this->resultArray("此分类的英文名重复,请重试", "failed");
        }
        if (!$validate->check($data)) {
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
     * @return void
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
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function update(Request $request, $id)
    {
        //
        $rule = [
            ["name", "require", "请输入文章分类名"],
            ["detail", "require", "请输入详情"],
            ["tag", "require", "请输入此分类的标签"]
        ];
        $data = $this->request->put();
        $data['create_time'] = strtotime($data['create_time']);
        $data['update_time'] = time();
        $user = $this->getSessionUser();
        $where['node_id'] = $user['user_node_id'];
        $where['alias'] = $data['alias'];
        $where['id'] = ['neq',$data['id']];
        $typedata = (new \app\admin\model\Articletype())->where($where)->select();
        if ($typedata) {
            return $this->resultArray("此分类的英文名重复,请重试", "failed");
        }
        $validate = new Validate($rule);
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), 'failed');
        }
        return $this->publicUpdate((new \app\admin\model\Articletype), $data, $id);
    }


    /**
     * 获取文章分类
     * @return array
     */
    public function getType()
    {
        $where = [];
        $user = $this->getSessionUser();
        $where['articletype.node_id'] = $user['user_node_id'];
        $data = (new \app\admin\model\Articletype())->getArttype($where);
        $dates=[];
        foreach ($data as$k=>$v){
            $dates[$v['tag']][] = ['id'=>$v['id'],'name'=>$v['name']];
        }
        return $this->resultArray('', '', $dates);
    }


    /**
     * 统计文章
     * @return array
     */
    public function ArticleCount()
    {
        $count = [];
        $name = [];
        foreach ($this->countArticle() as $item) {
            $count[] = $item["count"];
            $name[] = $item["name"];
        }
        $arr = ["count" => $count, "name" => $name];
        return $this->resultArray('', '', $arr);
    }

    public function countArticle()
    {
        $user = $this->getSessionUser();
        $where = [
            'node_id' => $user["user_node_id"],
        ];
        $articleTypes = \app\admin\model\Articletype::all($where);
        foreach ($articleTypes as $item) {
            yield $this->foreachArticle($item);
        }


    }

    public function foreachArticle($articleType)
    {
        $count = \app\admin\model\Article::where(["articletype_id" => $articleType->id])->count();
        return ["count" => $count, "name" => $articleType->name];

    }

}
