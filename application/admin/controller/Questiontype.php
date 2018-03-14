<?php

namespace app\admin\controller;

use think\Request;
use app\common\controller\Common;
use think\Validate;
use think\Db;
use app\admin\model\TypeTag as Type_Tag;

class Questiontype extends Common
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $request = $this->getLimit();
        $name = $this->request->get('name');
        $tag_id = $this->request->get('tag_id');
        $where = [];
        if (!empty($name)) {
            $where["name"] = ["like", "%$name%"];
        }
        if (!empty($tag_id)) {
            $where["tag_id"] = $tag_id;
        }
        $user = $this->getSessionUser();
        $where["node_id"] = $user["user_node_id"];
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
     * 获取文章分类
     * @return array
     */
    public function getType()
    {
        $where = [];
        $user = $this->getSessionUser();
        $where['type.node_id'] = $user['user_node_id'];
        $data = (new \app\admin\model\QuestionType())->getArttype($where);
        $dates=[];
        foreach ($data as$k=>$v){
            if(!$v['tag']){
                $dates['未定义'][] = ['id'=>$v['id'],'name'=>$v['name']];
            }else{
                $dates[$v['tag']][] = ['id'=>$v['id'],'name'=>$v['name']];
            }
        }
        return $this->resultArray('', '', $dates);
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
            ['name', 'require', "请填写分类名称"],
        ];
        $validate = new Validate($rule);
        $data = $request->post();
        $user = $this->getSessionUser();
        $where['node_id'] = $user['user_node_id'];
        $where['alias'] = $data['alias'];
        $typedata = (new \app\admin\model\QuestionType())->where($where)->select();
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
            $data['node_id'] = $user['user_node_id'];
            if (!$validate->check($data)) {
                return $this->resultArray($validate->getError(), "failed");
            }
            if (!\app\admin\model\QuestionType::create($data)) {
                exception("类型创建失败");
            }
            Db::commit();
        }catch (\Exception $e){
            Db::rollback();
            return $this->resultArray($e->getMessage(), "failed");
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
        return $this->getread((new \app\admin\model\QuestionType()), $id);
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
            ['name', 'require', "请填写分类名称"],
        ];
        $validate = new Validate($rule);
        $data = $request->put();
        $user = $this->getSessionUser();
        $where['node_id'] = $user['user_node_id'];
        $where['alias'] = $data['alias'];
        $where['id'] = ['neq',$data['id']];
        $typedata = (new \app\admin\model\QuestionType())->where($where)->select();
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
            if (!$this->publicUpdate((new \app\admin\model\QuestionType), $data, $id)) {
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
     * 删除指定资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        return $this->deleteRecord((new \app\admin\model\QuestionType), $id);
    }

    /**
     * 获取问答分类列表
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getQuestionType()
    {
        $field = "id,name";
        return $this->getList((new \app\admin\model\QuestionType()), $field);
    }





}
