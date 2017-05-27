<?php

namespace app\user\controller;

use app\admin\model\Site;
use think\Request;
use app\common\controller\Common;
use think\Validate;

class Index extends Common
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
     * 小网站用户存储站点信息
     * @return array
     */
    public function siteInfo()
    {
        $site_id=$this->request->post("site_id");
        $site_name=$this->request->post("site_name");
        $rule=[
            ["site_id","require","请选择站点"],
            ["site_name","require","请选择站点名称"]
        ];
        $validate=new Validate($rule);
        $data=$this->request->post();
        if(!$validate->check($data)){
            return $this->resultArray($validate->getError(), 'failed');
        }
        $site_info=Site::where(["id"=>$site_id,"site_name"=>$site_name])->find();
        if(!$site_info){
            return $this->resultArray('无此网站','failed');
        }
        $this->setSession($site_info);
        return $this->resultArray('','',$site_info);
    }

    /**
     * 设置session 全部都放进去 以后有用
     * @param $site_id
     * @param $site_name
     */
    public function setSession($site_info)
    {
        $arr["id"]=$site_info->getAttr("id");
        $arr["menu"]=$site_info->getAttr("menu");
        $arr["template_id"]=$site_info->getAttr("template_id");
        $arr["domain_id"]=$site_info->getAttr("domain");
        $arr["site_name"]=$site_info->getAttr("site_name");
        $arr["main_site"]=$site_info->getAttr("main_site");
        $arr["site_type"]=$site_info->getAttr("site_type");
        $arr["site_type_name"]=$site_info->getAttr("site_type_name");
        $arr["keyword_ids"]=$site_info->getAttr("keyword_ids");
        Session("website",$arr);
    }
}
