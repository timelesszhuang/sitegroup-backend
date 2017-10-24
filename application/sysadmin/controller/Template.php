<?php

namespace app\sysadmin\controller;

use app\common\controller\Common;
use think\Request;
use think\Validate;
use app\admin\model\Template as tem;
use app\common\traits\Obtrait;
class Template extends Common
{
    use Obtrait;
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $request = $this->getLimit();
        $name = $this->request->get('name');
        $where = [];
        if (!empty($name)) {
            $where["name"] = ["like", "%$name%"];
        }
        $where["node_id"] = ["lt", 1];
        $data = (new \app\admin\model\Template())->getTemplate($request["limit"], $request["rows"], $where);
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
            ["name","require","请先填写模板名"],
            ["thumbnails","require","请先上传缩略图"],
            ["path","require","请先上传替换后的模板"],
            ["show_path","require","请先上传未替换的模板"],
            ["industry_name","require","请上传行业名称"],
            ["industry_id","require","请上传行业id"]
        ];
        $validate=new Validate($rule);
        $post=$request->post();
        if(!$validate->check($post)){
            return $this->resultArray($validate->getError(),"failed");
        }
        $path="/upload/zipsrctemplate/";
        if(!file_exists(ROOT_PATH."public".$post["show_path"])){
            return $this->resultArray("未替换模板不存在","failed");
        }
        $src=ROOT_PATH."public".$post["show_path"];
        $obj=ROOT_PATH.'public'.$path;
        if($this->checkZipDirectory($src,$path)){
            return $this->resultArray("同名称模板已经存在,请修改","failed");
        }
        $show_path=$this->ZipArchive($src,$obj,$path);
        $post["show_path_href"]=$show_path;
        $tem=new tem();
        if(!$tem->allowField(true)->save($post)){
            return $this->resultArray("添加失败","failed");
        }
        return $this->resultArray("添加成功!!");
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        return $this->getread(new tem(),$id);
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
            ["name","require","请先填写模板名"],
            ["thumbnails","require","请先上传缩略图"],
            ["path","require","请先上传替换后的模板"],
            ["show_path","require","请先上传未替换的模板"],
            ["industry_name","require","请上传行业名称"],
            ["industry_id","require","请上传行业id"]
        ];
        $validate=new Validate($rule);
        $put=$request->put();
        if(!$validate->check($put)){
            return $this->resultArray($validate->getError(),"failed");
        }
        $path="/upload/zipsrctemplate/";
        if(!file_exists(ROOT_PATH."public".$put["show_path"])){
            return $this->resultArray("未替换模板不存在","failed");
        }
        $tem=tem::get($id);
        if(file_exists(ROOT_PATH."public".$tem->show_path_href)){
            $this->del_dir(ROOT_PATH."public".$tem->show_path_href);
        }
        $src=ROOT_PATH."public".$put["show_path"];
        $obj=ROOT_PATH.'public'.$path;
        $show_path=$this->ZipArchive($src,$obj,$path);
        $put["show_path_href"]=$show_path;
        if(!tem::update($put,["id"=>$id])){
            return $this->resultArray("修改失败!","failed");
        }
        return $this->resultArray("修改成功!!");
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
     * 上传嵌套后的模板文件
     * @return array
     */
    public function uploadPHPTemplate()
    {
        $request=Request::instance();
        $phptemp=$request->file('phptemplate');
        $path="/upload/template/";
        $info=$phptemp->move(ROOT_PATH."public".$path);
        if($info){
            return $this->resultArray("上传成功",'',$path.$info->getSaveName());
        }
        return $this->resultArray('上传失败',"failed");
    }

    /**
     * 上传原始模板
     * @return array
     */
    public function uploadTemplate()
    {
        $request=Request::instance();
        $template=$request->file("template");
        $path="/upload/srctemplate/";
        $info=$template->move(ROOT_PATH."public".$path);
        if($info){
            return $this->resultArray("上传成功","",$path.$info->getSaveName());
        }
        return $this->resultArray("上传失败","failed");
    }

    /**
     * 上传缩略图
     * @return array
     */
    public function uploadThumbnails()
    {
        $request=Request::instance();
        $thumb=$request->file("thumbnails");
        $path="/upload/srctemplate/";
        $info=$thumb->move(ROOT_PATH."public".$path);
        if($info){
            return $this->resultArray("上传成功",'',$path.$info->getSaveName());
        }
        return $this->resultArray("上传失败!","failed");
    }
}