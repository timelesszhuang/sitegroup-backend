<?php

namespace app\common\controller;

use app\common\model\SiteErrorInfo;
use app\common\model\SystemNoticeRead;
use think\Db;
use think\Request;
use app\common\model\SystemNotice as Sys;
use think\Validate;

class SystemNotice extends CommonLogin
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $request = $this->getLimit();
        $where = [];
        $user_info = $this->getSessionUserInfo();
        if ($user_info['user_type_name'] == 'node' && $user_info['user_type']==2) {
            $node = ','.$user_info["node_id"].',';
            $where["node_ids"] = ["like", "%$node%"];
        }elseif ($user_info['user_type'] == 1){
            $where = [];
        }
        $data = (new Sys())->getList($request["limit"], $request["rows"], $where);
        return $this->resultArray('', '', $data);
    }

    /**
     * 节点数据
     *
     */
    public function nodenotice(){
        $where = '';
        $user_info = $this->getSessionUserInfo();
        if ($user_info['user_type_name'] == 'node' && $user_info['user_type']==2) {
            $node = ','.$user_info["node_id"].',';
            $where=" node_ids like  '%$node%' ";
        }
        $data = Db::table('sg_system_notice')->alias('a')->field('a.*,c.status,c.node_id')->join('sg_system_notice_read c','a.id = c.notice_id','left')->where($where)->select();
        $datas['readdata'] = [];
        $datas['deldata'] = [];
        $datas['unreaddata'] = [];
        foreach ($data as $k=>$v){
            $v['time'] = $v['update_time']*1000;
            if($v['status'] == 10 ){
                $datas['readdata'][] = $v;
            }elseif ($v['status'] == 20 ||$v['status'] == null ){
                $datas['unreaddata'][] = $v;
                }elseif ($v['status'] == 30){
                $datas['deldata'][]  = $v;
            }
        }
        return $this->resultArray('','',$datas);

    }
    /**
     * 状态改变
     */
    public function readstatus(Request $request){
        $rule = [
            ["id", "require", "请传入id"],
            ["status", "require", "请输入状态"],
        ];
        $validate = new Validate($rule);
        $statusdata = $request->post();
        if (!$validate->check($statusdata)) {
            return $this->resultArray($validate->getError(), "failed");
        }
        $id = $statusdata['id'];
        $status =  $statusdata['status'];
        $user_info = $this->getSessionUserInfo();
        $where['node_id'] = $user_info["node_id"];
        $where['notice_id'] = $id;
        $readdata = (new SystemNoticeRead())->where($where)->find();
        if($status == 'read'){
            if(empty($readdata)){
                $Noticedata['notice_id'] = $id;
                $Noticedata['status'] = 20;
                $Noticedata['node_id'] =$user_info["node_id"];
                SystemNoticeRead::create($Noticedata);
                $data['status']=10;
            }else{
            $data['status'] = 10;}
        }elseif ($status == 'del'){
            $data['status'] = 30;
        }
        if (!(new SystemNoticeRead)->save($data, ["id" => $readdata['id']])) {
            return $this->resultArray('修改失败', 'failed');
        }
        return $this->resultArray('修改成功');

    }
    /**
     *未读数量
     */
    public function unreadnum(){

        $readdata = (new SystemNotice())->nodenotice();
        $num = count($readdata['data']['unreaddata'] );
         return $this->resultArray('','',$num);

    }


    /**
     * 获取错误信息
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getErrorInfo()
    {
        $user_info = $this->getSessionUserInfo();
        $where = [];
        if ($user_info['user_type_name'] == 'node' && $user_info['user_type']==2) {
            $where = [
                "node_id" => $user_info["node_id"],
            ];
        }
        $data = (new \app\common\model\SiteErrorInfo())->where($where)->field('site_id,node_id,create_time', true)->select();
        $datas['readdata'] = [];
        $datas['deldata'] = [];
        $datas['unreaddata'] = [];
        foreach ($data as $k=>$v ){
            $v['time'] = strtotime($v['update_time'])*1000;
            $v['title'] = $v['msg'];
            unset($v['msg']);
            unset($v['update_time']);
            if($v['status'] == '20'){
                $datas['unreaddata'][] = $v;
            }elseif ($v['status'] == '10'){
                $datas['readdata'][] = $v;
            }elseif ($v['status'] == '30'){
                $datas['deldata'][] = $v;
            }
        }
        return $this->resultArray('', '', $datas);
    }

    /**
     * 状态改变
     */
    public function error_status(Request $request){
        $rule = [
            ["id", "require", "请传入id"],
            ["status", "require", "请输入状态"],
        ];
        $validate = new Validate($rule);
        $statusdata = $request->post();
        if (!$validate->check($statusdata)) {
            return $this->resultArray($validate->getError(), "failed");
        }
        $id = $statusdata['id'];
        $status =  $statusdata['status'];
        if($status == 'read'){
            $data['status'] = 10;
        }elseif ($status == 'del'){
            $data['status'] = 30;
        }
        if (!(new SiteErrorInfo())->save($data, ["id" =>$id])) {
            return $this->resultArray( 'failed','修改失败');
        }
        return $this->resultArray('success','修改成功');

    }

    /**
     * 获取当前节点有多少没有查看的日志
     * @return array
     */
    public function getErrorStatus()
    {
        $user_info = $this->getSessionUserInfo();
        $where = [];
        if ($user_info['user_type_name'] == 'node' && $user_info['user_type']==2) {
            $where = [
                "node_id" => $user_info["node_id"],
                "status" => 20
            ];
        }
        $count = (new \app\common\model\SiteErrorInfo())->where($where)->count();
        if ($count < 1) {
            $count = "无";
        }
        return $this->resultArray('', '', $count);
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
            ["title", "require", "请输入标题"],
            ["content", "require", "请输入内容"],
        ];
        $validate = new Validate($rule);
        $data = $request->post();
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), "failed");
        }
        if(isset($data["node_ids"]) && !empty($data["node_ids"])){
            $ids=implode(",",$data["node_ids"]);
            $data["node_ids"]=",".$ids.",";
        }else{
            $nodeCollection=\app\common\model\Node::all();
            if(!empty($nodeCollection)){
                $nodeArr=collection($nodeCollection)->toArray();

                $nodeStr=implode(",",array_column($nodeArr,"id"));
                $data["node_ids"]=",".$nodeStr.",";
            }
        }
        if (!Sys::create($data)) {
            return $this->resultArray("添加失败", "failed");
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
        $find=Sys::where(["id"=>$id])->field("create_time,update_time", true)->find();
        if(!empty($find["node_ids"])){
            $find["node_ids"]=trim($find["node_ids"],",");
        }
        return $this->resultArray("","",$find);
    }


    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function readerror($id)
    {
        $find=(new SiteErrorInfo)->where(["id"=>$id])->field("create_time,update_time", true)->find();
        return $this->resultArray("","",$find);
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
            ["title", "require", "请输入标题"],
            ["content", "require", "请输入内容"],
        ];
        $validate = new Validate($rule);
        $data = $request->post();
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), "failed");
        }
        if(isset($data["node_ids"]) && !empty($data["node_ids"])){
            $ids=implode(",",$data["node_ids"]);
            $data["node_ids"]=",".$ids.",";
        }else{
            $nodeCollection=\app\common\model\Node::all();
            if(!empty($nodeCollection)){
                $nodeArr=collection($nodeCollection)->toArray();
                $nodeStr=implode(",",array_column($nodeArr,"id"));
                $data["node_ids"]=",".$nodeStr.",";
            }
        }
        if (!(new Sys)->save($data, ["id" => $id])) {
            return $this->resultArray('修改失败', 'failed');
        }
        return $this->resultArray('修改成功');
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $Sys = Sys::get($id);
        if (!$Sys->delete()) {
            return $this->resultArray('删除失败', 'failed');
        }
        return $this->resultArray('删除成功');
    }

//    /**
//     * 获取node节点
//     * @return array
//     */
//    public function nodeList()
//    {
//        $data=\app\common\model\Node::where(1)->field(["id,name"])->select();
//        return $this->resultArray('','',$data);
//    }

    /**
     * @param Request $request
     * @param $id
     * @return array
     * 阅读递加1
     */
    public function readcount(Request $request, $id){
        $Sys = Sys::get($id);
        $data['readcount']= $Sys['readcount']+1;
        if (!(new Sys)->save($data, ["id" => $id])) {
            return $this->resultArray('修改失败', 'failed');
        }

    }
}
