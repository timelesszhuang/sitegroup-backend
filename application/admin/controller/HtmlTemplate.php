<?php

namespace app\admin\controller;

use app\common\controller\Common;
use app\common\model\EventMarketingHolidayRecord;
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
        $request=Request::instance();
        $id=$request->post('id');
        $content=$request->post('content');
        if(empty($id) || empty($content)){
            return $this->resultArray("请传递参数",'failed');
        }
        $data=(new Html)->where(["id"=>$id])->find();
        if(empty($data)){
            return $this->resultArray("模板未获取到",'failed');
        }
        $realy="生成失败";
        if(is_file(ROOT_PATH."public/upload/".$data["generated_path"]."/index.html")){
            $file_name = mb_substr(md5(uniqid(rand(), true)),10,10);
            $realy=file_put_contents(ROOT_PATH."public/upload/".$data["generated_path"]."/".$file_name.".html",$content);
            if($realy){
                $user = $this->getSessionUser();
                $eventMark=EventMarketingHolidayRecord::where(["node_id"=>$user["user_node_id"],"holiday_id"=>$data["holiday_id"]])->find();
                if(empty($eventMark)){
                    EventMarketingHolidayRecord::create([
                        "node_id"=>$user["user_node_id"],
                        "template_name"=>$data["template_name"],
                        "holiday"=>$data["holiday_name"],
                        "holiday_id"=>$data["holiday_id"],
                        "img"=>$data["img"],
                        "path"=>$data["generated_path"]."/".$file_name.".html"
                    ]);
                }else{
                    echo ROOT_PATH."public/upload/".$data["path"];die;
                    if(is_file(ROOT_PATH."public/upload/".$data["path"])){
                        unlink(ROOT_PATH."public/upload/".$data["path"]);
                    }
                    $eventMark->path=$data["generated_path"]."/".$file_name.".html";
                    $eventMark->save();
                }
                $realy="生成成功";
                return $this->resultArray($realy,'',"http://api.salesman.cc/upload/".$data["generated_path"]."/".$file_name.".html");
            }
        }
        return $this->resultArray($realy,'failed');
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
        $data=(new Html)->where(["id"=>$id])->field(["generated_path,template_name"])->find();
        $fdata["title"]=$data["template_name"];
        $fdata["data"]="error";
        if(is_file(ROOT_PATH."public/upload/".$data["generated_path"]."/index.html")){
            $fdata["data"]=file_get_contents(ROOT_PATH."public/upload/".$data["generated_path"]."/index.html");
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
