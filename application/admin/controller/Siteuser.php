<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\common\controller\Common;
use think\Validate;
class Siteuser extends Common
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $limits = $this->getLimit();
        $name = $this->request->get('name');
        $where = [];
        if (!empty($name)) {
            $where['name'] = $name;
        }
        $user = $this->getSessionUser();
        $where["node_id"] = $user["user_node_id"];
        return $this->resultArray('', '', (new \app\admin\model\SiteUser())->getAll($limits['limit'], $limits['rows'], $where));
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
            ['name','require','请填写昵称'],
            ['pwd', 'require', "请填写密码"],
            ['account','require','请填写帐号'],
            ['mobile','require','请填写手机号'],
            ['confirmPwd',"require","请填写确认密码"]
        ];
        $validate = new Validate($rule);
        $data = $this->request->post();
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), 'failed');
        }
        if($data["pwd"]!=$data["confirmPwd"]){
            return $this->resultArray('两次输入的密码不同', 'failed');
        }
        unset($data["confirmPwd"]);
        $data["node_id"] = $this->getSessionUser()['user_node_id'];
        if (!\app\admin\model\SiteUser::create($data)) {
            return $this->resultArray('添加失败', 'failed');
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
        return $this->resultArray('', '', (new \app\admin\model\SiteUser)->where(["id" => $id])->field("id,name,account,com_name,is_on,email,mobile")->find());
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
            ['name','require','请填写昵称'],
            ['account','require','请填写帐号'],
            ['mobile','require','请填写手机号'],
        ];
        $validate = new Validate($rule);
        $data = $this->request->put();
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), 'failed');
        }
        $user=$this->getSessionUser();
        $where=[
            "id"=>$id,
            "node_id"=>$user["user_node_id"]
        ];

        //前台可能会提交id过来,为了防止错误,所以将其删除掉
        if(array_key_exists('id',$data)){
            unset($data["id"]);
        }
        if (!(new \app\admin\model\SiteUser)->save($data,$where)) {
            return $this->resultArray('修改失败', 'failed');
        }
        return $this->resultArray('修改成功');
    }

    /**
     * 是否启用
     * @param  int  $id
     * @return \think\Response
     */
    public function enable($id)
    {
        $is_on=$this->request->put("is_on");
        if(empty($is_on)){
            return $this->resultArray('请传递参数','failed');
        }
        $user=$this->getSessionUser();
        $where=[
            "id"=>$id,
            "node_id"=>$user["user_node_id"]
        ];
        $user=(new \app\admin\model\SiteUser)->where($where)->update([
            "is_on"=>$is_on
        ]);
        if(!$user){
            return $this->resultArray('修改失败','failed');
        }
        return $this->resultArray('修改成功');
    }

    /**
     * 获取所有用户
     * @return array
     */
    public function getUsers()
    {
        $field="id,name as text";
        return $this->getList((new \app\admin\model\SiteUser),$field);
    }
}
