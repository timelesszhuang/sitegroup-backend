<?php

namespace app\common\controller;

use app\common\controller\Common;
use think\Request;
use think\Validate;
use app\common\model\Menutag as mtag;
class Menutag extends CommonLogin
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
        $where = [];
        $user_info = $this->getSessionUserInfo();
        $where["node_id"] =$user_info["node_id"];
        if (!empty($name)) {
            $where["name"] = ["like", "%$name%"];
        }
        $data = (new mtag())->getArticle($request["limit"], $request["rows"], $where);
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
            ['name', "require", "请填写分类名"],
            ['detail', 'require', "请填写描述"],
        ];
        $validate = new Validate($rule);
        $data = $this->request->post();
        $user_info = $this->getSessionUserInfo();
        $data['node_id'] =$user_info["node_id"];
        if (!$validate->check($data)) {
            return $this->resultArray('failed',$validate->getError() );
        }
        if (!mtag::create($data)) {
            return $this->resultArray('failed','添加失败' );
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
        return $this->getread((new mtag), $id);
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
            ['name', "require", "请填写分类名"],
            ['detail', 'require', "请填写描述"],
        ];
        $validate = new Validate($rule);
        $data = $this->request->put();
        $user_info = $this->getSessionUserInfo();
        $data['node_id'] =$user_info["node_id"];
        if (!$validate->check($data)) {
            return $this->resultArray( 'failed',$validate->getError());
        }
        return $this->publicUpdate((new mtag),$data,$id);
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }

    /**
     * 获取所有code
     * @return array
     */
    public function getTags()
    {
        $field="id,name as text";
        $where=[];
        $user_info = $this->getSessionUserInfo();
        $where["node_id"] =$user_info["node_id"];
        $data = (new mtag)->field($field)->where($where)->select();
        return $this->resultArray('', '', $data);
    }
}
