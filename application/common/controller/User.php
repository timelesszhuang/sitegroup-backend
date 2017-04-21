<?php

namespace app\common\controller;

use think\Controller;
use think\Request;

class User extends Common
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        if ($this->request->isGet()) {
            $request = $this->getLimit();
            return (new \app\common\model\User)->getUser($request['limit'], $request["rows"]);
        }
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //
    }

    /**
     * 显示指定的资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function read($id)
    {
        if($this->request->isGet()){
            $user = new \app\common\model\User;
            return $user->field("id,user_name,type,name,tel,mobile,qq,wechat,email,create_time")->where(["id"=>$id])->find();
        }

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
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int $id
     * @return \think\Response
     * auther jingzheng
     */
    public function delete($id)
    {
        if ($this->request->isDelete()) {
//            return 1111;die;
            if ($id == 1) {
                return $this->resultArray('系统管理员不允许删除', 'failed');
            }
            $user = \app\common\model\User::get($id);
            if (!$user->delete()) {
                return $this->resultArray('删除失败','failed');
            }
            return $this->resultArray('删除成功');

        }
    }
}
