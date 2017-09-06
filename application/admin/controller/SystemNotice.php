<?php

namespace app\admin\controller;

use app\admin\model\SystemNoticeRead;
use app\common\controller\Common;
use think\Request;
use app\admin\model\SystemNotice as Sys;
class SystemNotice extends Common
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $user = $this->getSessionUser();
        $node_id=$user["user_node_id"];
        $request = $this->getLimit();
        $where=[];
        $data = (new Sys())->getList($request["limit"], $request["rows"],$node_id, $where);
        return $this->resultArray('', '', $data);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        $user = $this->getSessionUser();
        $read=SystemNoticeRead::where(["node_id"=>$user["user_node_id"]])->field(["notice_id"])->select();
        $where=[];
        if(!empty($read)){
            $data=collection($read)->toArray();
            $where["id"]=["not in", array_column($data,"notice_id")];
        }
        $count=Sys::where($where)->count();
        return $this->resultArray('','',$count);
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
        $user = $this->getSessionUser();
        if(!SystemNoticeRead::where([
            "node_id"=>$user["user_node_id"],
            "notice_id"=>$id
        ])->find()){
            SystemNoticeRead::create([
                "node_id"=>$user["user_node_id"],
                "notice_id"=>$id
            ]);
        }
        return $this->getread((new Sys), $id);
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
