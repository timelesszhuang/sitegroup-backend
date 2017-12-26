<?php

namespace app\user\controller;

use app\common\controller\Common;
use think\Request;

class Site extends Common
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $site_id=$this->getSiteSession('website')["id"];
        if(empty($site_id)){
            return $this->resultArray("获取站点错误","failed");
        }
        $site=\app\admin\model\Site::get($site_id);
        if(empty($site)){
            return $this->resultArray("获取站点错误","failed");
        }
        return $this->resultArray("","",$site);
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
        $site_id=$this->getSiteSession('website')["id"];
        if(empty($site_id)){
            return $this->resultArray("获取站点错误","failed");
        }
        $rule = [
        ];
        $validate = new Validate($rule);
        $data = \request()->post();
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), "failed");
        }
        $id = $data['id'];
        unset($data['id']);
        if(!\app\admin\model\Site::update($data,["id"=>$site_id])){
            return $this->resultArray("修改站点失败!!","failed");
        }
        return $this->resultArray("修改成功!!");
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
