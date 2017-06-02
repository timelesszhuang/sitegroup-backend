<?php

namespace app\common\controller;

use app\common\model\AccessKeyword;
use app\common\model\BrowseRecord;
use think\Controller;
use think\Request;

class ExternalAccess extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
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
        $browse=new BrowseRecord($request->post());
        $browse->allowField(true)->save();
        $keyword=$request->post("keyword");
        if(!empty($keyword)){
            $where=[
                "keyword"=>$keyword,
                "site_id"=>intval($request->post("site_id")),
                "node_id"=>intval($request->post("node_id"))
            ];
            $access=AccessKeyword::where($where)->find();
            if($access){
                $access->count=++$access->count;
                $access->save();
                return;
            }
            $access_model=new AccessKeyword($request->post());
            $access_model->allowField(true)->save();
        }
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
