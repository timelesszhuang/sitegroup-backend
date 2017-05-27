<?php

namespace app\common\controller;

use think\Request;
use think\Validate;

class User extends Common
{
    /**
     * 显示资源列表
     * @return \think\Response
     * @author guozhen
     */
    public function index()
    {
        $request = $this->getLimit();
        $type=$this->request->get("type");
        $company=$this->request->get("name");
        $where=[];
        if(!empty($type)){
            $where["type"]=["eq",$type];
        }
        if(!empty($company)){
            $where["name"]=["like","%$company%"];
        }
        return $this->resultArray('','',(new \app\common\model\User)->getUser($request['limit'], $request["rows"],$where));
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
     * @author guozhen
     */
    public function save(Request $request)
    {
        $rule = [
            ["user_name", "require|unique:User", "请输入用户名|用户名重复"],
            ["pwd", "require", "请输入密码"],
            ["contacts", "require", "请输入联系人"],
            ["mobile", "require", "请输入电话"],
            ["type_name","require","请选择类型"],
            ["type","require","请选择类型"],
            ["name","require","请输入公司名称"]
        ];
        $data = $this->request->post();
        $validate = new Validate($rule);
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), 'failed');
        }
        if (!\app\common\model\User::create($data)) {
            return $this->resultArray('添加失败', 'failed');
        }
        return $this->resultArray('添加成功');
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     * @author guozhen
     */
    public function read($id)
    {
        $user = new \app\common\model\User;
        return $this->resultArray('','',$user->where(["id" => $id])->find());
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
     * @author guozhen
     */
    public function update(Request $request, $id)
    {
        $rule = [
            ["user_name", "require", "请输入用户名"],
            ["contacts", "require", "请输入联系人"],
            ["mobile", "require", "请输入电话"],
            ["type_name","require","请选择类型"],
            ["type","require","请选择类型"],
            ["name","require","请输入公司名称"]
        ];
        $data = $request->put();
        $validate = new Validate($rule);
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), 'failed');
        }
        if (!\app\common\model\User::update($data)) {
            return $this->resultArray('修改失败', 'failed');
        }
        return $this->resultArray('修改成功');
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     * @author guozhen
     */
    public function delete($id)
    {
        $user = \app\common\model\User::get($id);
        if($user->type==1){
            return $this->resultArray('不允许删除', 'failed');
        }
        if(!empty($user->node_id)){
            return $this->resultArray('不允许删除', 'failed');
        }
        if (!$user->delete()) {
            return $this->resultArray('删除失败', 'failed');
        }
        return $this->resultArray('删除成功');
    }

    /**
     * 获取所有用户 id,username
     * @return array
     */
    public function getAll()
    {
        $where=[];
        $where["type"]=2;
        $where["node_id"]=0;
        $data=(new \app\common\model\User)->where($where)->field("id,user_name as name")->select();
        return $this->resultArray('','',$data);
    }
}

