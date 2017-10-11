<?php

namespace app\admin\controller;

use app\common\controller\Common;
use app\common\model\Media;
use app\common\model\MediaType;
use think\Request;
use app\common\model\SoftText as Soft;
use think\Validate;

class SoftText extends Common
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $request = $this->getLimit();
        $title = $this->request->get('title');
        $where = [];
        if (!empty($title)) {
            $where["title"] = ["like", "%$title%"];
        }
        $data = (new Soft())->getAll($request["limit"], $request["rows"], $where);
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
        $rule=[
            ["media_id","require","请输入媒体id"],
            ["media_name","require","请输入媒体名称"],
            ["title","require","请输入标题"],
            ["content","require","请输入内容"],
            ["origin","require","请选择地区"],
            ["origin_id","require","请选择地区"]
        ];
        $validate=new Validate($rule);
        $post=$request->post();
        if(!$validate->check($post)){
            return $this->resultArray($validate->getError(),"failed");
        }
        $user = $this->getSessionUser();
        $post["node_id"]=$user["user_node_id"];
        $post["node_name"] = $user["user_commpany_name"];
        if(!Soft::create($post)){
            return $this->resultArray("添加失败!",'failed');
        }
        return $this->resultArray("添加成功!");
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        return $this->getread((new Soft()),$id);
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
        $rule=[
            ["media_id","require","请输入媒体id"],
            ["media_name","require","请输入媒体名称"],
            ["title","require","请输入标题"],
            ["content","require","请输入内容"],
            ["origin","require","请选择地区"],
            ["origin_id","require","请选择地区"]
        ];
        $validate=new Validate($rule);
        $put=$request->put();
        if(!$validate->check($put)){
            return $this->resultArray($validate->getError(),"failed");
        }
        $user = $this->getSessionUser();
        $put["node_id"]=$user["user_node_id"];
        $put["node_name"] = $user["user_commpany_name"];
        if(!(new Soft)->save($put,["id"=>$id])){
             return $this->resultArray("修改失败",'failed');
        }
        return $this->resultArray("修改成功!");
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $user = $this->getSessionUser();
        $where = [
            "id" => $id,
            "node_id" => $user["user_node_id"],
            "is_check"=>1
        ];
        if (!(new Soft)->where($where)->delete()) {
            return $this->resultArray('删除失败', 'failed');
        }
        return $this->resultArray('删除成功');
    }

    /**
     * 获取地区
     * @return array
     */
    public function getOrigin()
    {
        $arr=[
            ["id"=>1,"text"=>"北京"],
            ["id"=>2,"text"=>"上海"],
            ["id"=>3,"text"=>"广东"],
            ["id"=>4,"text"=>"浙江"],
            ["id"=>5,"text"=>"天津"],
            ["id"=>6,"text"=>"重庆"],
            ["id"=>7,"text"=>"湖北"],
            ["id"=>8,"text"=>"湖南"],
            ["id"=>9,"text"=>"河北"],
            ["id"=>10,"text"=>"河南"],
            ["id"=>11,"text"=>"山东"],
            ["id"=>12,"text"=>"山西"],
            ["id"=>13,"text"=>"江苏"],
            ["id"=>14,"text"=>"江西"],
            ["id"=>15,"text"=>"四川"],
            ["id"=>16,"text"=>"辽宁"],
            ["id"=>17,"text"=>"吉林"],
            ["id"=>18,"text"=>"福建"],
            ["id"=>19,"text"=>"安徽"],
            ["id"=>20,"text"=>"黑龙江"]
        ];
        return $this->resultArray('修改成功','',$arr);
    }


    /**
     * 根据地区获取媒体分类
     * @param $id
     * @return array
     */
    public function returnsOrigin($id)
    {
        $data=Media::where(["origin_id"=>$id])->field("id,concat_ws('-----',name,media_type_name) as text")->select();
        if(empty($data)){
            return $this->resultArray('当前地区没有媒体分类','failed');
        }
        $old_data=collection($data)->toArray();
        return $this->resultArray('', '', $old_data);
    }

}
