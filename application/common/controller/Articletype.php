<?php

namespace app\common\controller;

use app\common\model\Menu;
use think\Db;
use think\Validate;
use think\Request;
use app\common\model\Articletype as this_model;
use app\common\model\TypeTag as Type_Tag;

class Articletype extends CommonLogin
{
    /**
     * @return array
     */
    //TODO oldfunction
    public function index()
    {
        $request = $this->getLimit();
        $name = $this->request->get('name');
        $id = $this->request->get('id');
        $tag_id = $this->request->get('tag_id');
        $where = [];
        if (!empty($name)) {
            $where["name"] = ["like", "%$name%"];
        }
        if (!empty($id)) {
            $where["id"] = $id;
        }
        if (!empty($tag_id)) {
            $where["tag_id"] = $tag_id;
        }
        $user = $this->getSessionUser();
        $where["node_id"] = $user["user_node_id"];
        $data = (new \app\common\model\Articletype())->getArticletype($request["limit"], $request["rows"], $where);
        return $this->resultArray('', '', $data);
    }

    /**
     * @param $id
     * @return array
     */
    //TODO oldfunction
    public function read($id)
    {
        return $this->resultArray('', '', \app\common\model\Articletype::get($id));
    }

    /**
     * 显示创建资源表单页.
     *
     * @return void
     */
    //TODO oldfunction
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
    //TODO oldfunction
    public function save(Request $request)
    {
        $rule = [
            ["name", "require", "请输入文章分类名"],
            ["detail", "require", "请输入详情"],
            ["tag_id", "require", "请输入或选择分类"],
            ["alias", "require", "请输入此分类的英文名"]
        ];
        $validate = new Validate($rule);
        $data = $this->request->post();
        $user = $this->getSessionUser();
        $data['node_id'] = $user['user_node_id'];
        $where['node_id'] = $user['user_node_id'];
        $where['alias'] = $data['alias'];
        $typedata = (new \app\common\model\Articletype())->where($where)->select();
        Db::startTrans();
        try{
            if ($typedata) {
                exception("此分类的英文名重复,请重试");
            }
            if(isset($data['tag_name'])&&$data['tag_name']){
                $where1['node_id'] = $user['user_node_id'];
                $where1['tag'] = $data['tag_name'];
                $Type_Tag = new Type_Tag;
                $typetag = $Type_Tag->where($where1)->find();
                if($typetag){
                    $data['tag_id']=$typetag['id'];
                }else{
                    $data_tag['tag'] = $data['tag_name'];
                    $data_tag['node_id'] = $user['user_node_id'];
                    if (!$Type_Tag::create($data_tag)) {
                        exception("标签创建失败");
                    }
                    $data['tag_id']=$Type_Tag->getLastInsID();
                }
            }
            unset($data['tag_name']);
            if (!$validate->check($data)) {
                return $this->resultArray($validate->getError(), "failed");
            }
            if (!\app\common\model\Articletype::create($data)) {
                exception("类型创建失败");
            }
            Db::commit();
        }catch (\Exception $e){
            Db::rollback();
            return $this->resultArray($e->getMessage(), "failed");
        }

        return $this->resultArray("添加成功");
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int $id
     * @return void
     */
    //TODO oldfunction
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
    //TODO oldfunction
    public function update(Request $request, $id)
    {
        //
        $rule = [
            ["name", "require", "请输入文章分类名"],
            ["detail", "require", "请输入详情"],
            ["tag_id", "require", "请输入或选择分类"],
            ["alias", "require", "请输入此分类的英文名"]
        ];
        $validate = new Validate($rule);
        $data = $this->request->put();
        unset($data['create_time']);
        $data['update_time'] = time();
        $user = $this->getSessionUser();
        $where['node_id'] = $user['user_node_id'];
        $where['alias'] = $data['alias'];
        $where['id'] = ['neq',$id];
        $typedata = (new \app\common\model\Articletype())->where($where)->select();
        Db::startTrans();
        try{
            if ($typedata) {
                exception("此分类的英文名重复,请重试");
            }
            if(isset($data['tag_name'])&&$data['tag_name']){
                $where1['node_id'] = $user['user_node_id'];
                $where1['tag'] = $data['tag_name'];
                $Type_Tag = new Type_Tag;
                $typetag = $Type_Tag->where($where1)->find();
                if($typetag){
                    $data['tag_id']=$typetag['id'];
                }else{
                    $data_tag['tag'] = $data['tag_name'];
                    $data_tag['node_id'] = $user['user_node_id'];
                    if (!$Type_Tag::create($data_tag)) {
                        exception("标签创建失败");
                    }
                    $data['tag_id']=$Type_Tag->getLastInsID();
                }
            }
            unset($data['tag_name']);
            if (!$validate->check($data)) {
                return $this->resultArray($validate->getError(), "failed");
            }
            if (!$this->publicUpdate((new \app\common\model\Articletype), $data, $id)) {
                exception("类型修改失败");
            }
            Db::commit();
            return $this->resultArray("修改成功");
        }catch (\Exception $e){
            Db::rollback();
            return $this->resultArray($e->getMessage(), "failed");
        }
    }


    /**
     * 获取文章分类
     * @return array
     * @throws \app\common\exception\ProcessException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getType()
    {
        $user_info = $this->getSessionUserInfo();
        if ($user_info['user_type_name']=='node'){
            $data = (new this_model())->getArticleTypeByNodeId($user_info['node_id']);
        }elseif($user_info['user_type_name']=='site'){
            $type_ids = (new Menu())->getSiteTypeIds($user_info['user_id'],3);
            $data = (new this_model())->getArticleTypeByIdArray($type_ids);
        }else{
            Common::processException('未知错误');
        }
        $dates=[];
        /** @var array $data */
        foreach ($data as $k=> $v){
            if(!$v['tag']){
                $dates['未定义'][] = ['id'=>$v['id'],'name'=>$v['name']];
            }else{
                $dates[$v['tag']][] = ['id'=>$v['id'],'name'=>$v['name']];
            }
        }
        return $this->resultArray('success', '获取成功', $dates);
    }

    /**
     * 统计文章
     * @return array
     */
    //TODO oldfunction
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

    //TODO oldfunction
    public function countArticle()
    {
        $user = $this->getSessionUser();
        $where = [
            'node_id' => $user["user_node_id"],
        ];
        $articleTypes = \app\common\model\Articletype::all($where);
        foreach ($articleTypes as $item) {
            yield $this->foreachArticle($item);
        }


    }

    //TODO oldfunction
    public function foreachArticle($articleType)
    {
        $count = \app\common\model\Article::where(["articletype_id" => $articleType->id])->count();
        return ["count" => $count, "name" => $articleType->name];

    }

}
