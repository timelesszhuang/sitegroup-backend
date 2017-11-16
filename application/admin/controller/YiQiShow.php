<?php

namespace app\admin\controller;

use app\common\controller\Common;
use think\Request;
use think\Db;
use app\common\model\User;
class YiQiShow extends Common
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $url="http://xiu.hi-link.com.cn/index.php?c=user&a=login";
        $user = $this->getSessionUser();
        if(!isset($user["user_id"]) || empty($user["user_id"])){
            exit("当前用户获取失败");
        }
        $getUser=User::get(["id"=>$user["user_id"]]);
        if(empty($getUser)){
            exit("当前用户获取失败");
        }
        $yqx=Db::connect("db1")->name("cj_users")->where(["email_varchar"=>$getUser->yqx_account])->find();
        if(empty($yqx)){
            exit("当前用户获取失败");
        }
        $lxy_token=md5($getUser->user_name.time());
        if(!Db::connect("db1")->name("cj_users")->where(["userid_int"=>$yqx["userid_int"]])->update(["lxy_token"=>$lxy_token])){
            exit("当前用户获取失败");
        }
//        $this->redirect($url."&token=$lxy_token");
        return $this->resultArray('',"",$url."&token=$lxy_token");
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
        //
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
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
        //
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
}
