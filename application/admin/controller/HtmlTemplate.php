<?php

namespace app\admin\controller;

use app\common\controller\Common;
use think\Request;
use app\common\model\HtmlTemplate as Html;

class HtmlTemplate extends Common
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
        $request=Request::instance();
        $callback=$request->get('callback');
        $data=(new Html)->where(["id"=>$id])->field(["generated_path"])->find();
        $fdata["data"]="error";
        if(is_file(ROOT_PATH."public/upload/".$data["generated_path"])){
            $fdata["data"]=file_get_contents(ROOT_PATH."public/upload/".$data["generated_path"]);
        }
         exit($callback . '(' . json_encode($fdata) .')');
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

    /**
     * 获取多条
     * @param $id
     * @return array
     */
    public function readAll($id)
    {
        $data=(new Html)->where(["holiday_id"=>$id])->field(["id,img,path,holiday_id,holiday_name,template_name"])->select();
        return $this->resultArray('','',$data);
    }
}
