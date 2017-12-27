<?php

namespace app\admin\controller;

use app\common\controller\Common;
use think\Request;
use app\admin\model\Producttype as produ;
use think\Validate;
use think\Db;
use app\admin\model\TypeTag as Type_Tag;

class ProductType extends Common
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
        $data = (new produ())->getAll($request["limit"], $request["rows"], $where);
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
            ["name", "require", "请输入产品分类"],
            ["detail", "require", "请输入描述"],
        ];
        $validate = new Validate($rule);
        $data = $request->post();
        $user = $this->getSessionUser();
        $data['node_id'] = $user['user_node_id'];
        $where['node_id'] = $user['user_node_id'];
        $where['alias'] = $data['alias'];
        $typedata = (new \app\admin\model\Producttype())->where($where)->select();
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
            if (!\app\admin\model\Producttype::create($data)) {
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
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        return $this->getread((new produ), $id);
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
        $data = (new \app\admin\model\Producttype())->getArttype($where);
        $dates=[];
        foreach ($data as$k=>$v){
            $dates[$v['tag']][] = ['id'=>$v['id'],'name'=>$v['name']];
        }
        return $this->resultArray('', '', $dates);
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
            ["name", "require", "请输入产品分类"],
            ["detail", "require", "请输入描述"],
        ];
        $validate = new Validate($rule);
        $data = $request->put();
        $user = $this->getSessionUser();
        $where['node_id'] = $user['user_node_id'];
        $where['alias'] = $data['alias'];
        $where['id'] = ['neq',$data['id']];
        $typedata = (new \app\admin\model\Producttype())->where($where)->select();
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
            if (!$this->publicUpdate((new \app\admin\model\Producttype), $data, $id)) {
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
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        return $this->deleteRecord((new produ), $id);
    }

    /**
     * 获取所有type
     * @return array
     */
    public function getTypes()
    {
        $field="id,name as text";
        return $this->getList((new produ),$field);
    }
}
