<?php

namespace app\sysadmin\controller;

use app\common\controller\Common;
use think\Request;

class Template extends Common
{
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

    /**
     * 上传嵌套后的模板文件
     * @return array
     */
    public function uploadPHPTemplate()
    {
        $request=Request::instance();
        $phptemp=$request->file('phptemplate');
        $path="/upload/template/";
        $info=$phptemp->move(ROOT_PATH."public/".$path);
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
        $info=$template->move(ROOT_PATH."public/".$path);
        if($info){
            return $this->resultArray("上传成功","",$path.$info->getSaveName());
        }
        return $this->resultArray("上传失败","failed");
    }
}
