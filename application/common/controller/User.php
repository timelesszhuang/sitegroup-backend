<?php

namespace app\common\controller;

use think\Controller;
use think\Request;

class User extends Common
{
    /**
     * 显示资源列表
     * @return \think\Response
     * @auther guozhen
     */
    public function index()
    {
        if ($this->request->isGet()) {
            $request = $this->getLimit();
            return (new \app\common\model\User)->getUser($request['limit'], $request["rows"]);
        }
    }

    /**
     * 显示指定的资源
     *
     * @param  int $id
     * @return \think\Response
     * @auther guozhen
     */
    public function read($id)
    {
        if($this->request->isGet()){
            $user = new \app\common\model\User;
            return $user->field("id,user_name,type,name,tel,mobile,qq,wechat,email,create_time")->where(["id"=>$id])->find();
        }

    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request $request
     * @param  int $id
     * @return \think\Response
     */
    public function update($id)
    {
        if($this->request->isPut()){
            $rule=[
                ["user_name","require","请输入用户名"],
                ["pwd","require","请输入密码"],
                []
            ];
            $data=\app\common\model\User::create();

        }


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
