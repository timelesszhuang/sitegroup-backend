<?php

namespace app\admin\controller;

use app\common\controller\Common;

use think\Request;
use app\common\model\SiteLogo as site;
use app\common\traits\Osstrait;
use app\common\traits\Obtrait;
use think\Validate;
class SiteLogo extends Common
{
    use Osstrait;
    use Obtrait;
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $limit = $this->getLimit();
        $user = $this->getSessionUser();
        $where["node_id"] = $user["user_node_id"];
        $data=(new site())->getAll($limit["limit"], $limit["rows"], $where);
        return $this->resultArray("",'',$data);
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
            ["oss_logo_path","require","请先上传logo"],
            ["name","require","请输入名称"]
        ];
        $post=$request->post();
        $validate=new Validate($rule);
        if(!$validate->check($post)){
            return $this->resultArray($validate->getError(),"failed");
        }
        $user = $this->getSessionUser();
        $post["node_id"] = $user["user_node_id"];
        if(!site::create($post)){
            return $this->resultArray("添加失败","failed");
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
        return $this->getread((new site),$id);
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
            ["oss_logo_path","require","请先上传图片"],
            ["name","require","请输入名称"]
        ];
        $put=$request->put();
        $validate=new Validate($rule);
        if(!$validate->check($put)){
            return $this->resultArray($validate->getError(),"failed");
        }
        if((new site)->update($put,["id"=>$id])){
            return $this->resultArray("修改成功");
        }
        return $this->resultArray("修改失败");
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
     * 网站logo上传
     * @return array
     */
    public function uploadLoginImg()
    {
        $data=$this->uploadImg('sitelogo');
        if($data["status"]){
            $data["msg"]="上传成功";
            return $data;
        }
        return $this->resultArray('上传失败，请重新上传!',"failed");
    }

    /**
     * 获取列表
     * @return array
     */
    public function logoList()
    {
        $user = $this->getSessionUser();
        $where["node_id"] = $user["user_node_id"];
        $sites=(new site)->where($where)->field(["id,name,oss_logo_path"])->select();
        return $this->resultArray("","",$sites);
    }
}
