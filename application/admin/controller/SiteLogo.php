<?php

namespace app\admin\controller;

use app\common\controller\Common;

use think\Request;
use app\common\model\SiteLogo as site;
class SiteLogo extends Common
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $limit = $this->getLimit();
        $data=(new site())->getAll($limit["limit"], $limit["rows"], '');
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
     * 网站logo上传
     * @return array
     */
    public function uploadImg()
    {
        $data=$this->uploadImg('sitelogo');
        if($data["status"]){
            $data["msg"]="上传成功";
            return $data;
        }
        return $this->resultArray('上传失败，请重新上传!',"failed");
    }
}
