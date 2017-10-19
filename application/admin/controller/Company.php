<?php

namespace app\admin\controller;

use app\common\controller\Common;
use app\sysadmin\model\Node;
use think\Request;
use app\sysadmin\model\Company as Com;
use think\Validate;

class Company extends Common
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
        $data = (new Com())->getCompany($request["limit"], $request["rows"], $where);
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

    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        $user = $this->getSessionUser();
        $node_id = $user["user_node_id"];
        $node=Node::get($node_id);
        if(empty($node->com_id)){
            return $this->resultArray('查询错误',"failed");
        }
        return $this->getread((new Com),$node->com_id);
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
            ["name","require","请输入机构名称"],
            ["industry_id","require","请输入行业名称"],
            ["industry_name","require","请输入行业名称"],
            ["tax_registration_number","require","请输入税务登记号"],
            ["business_license","require","请上传营业执照"],
            ["artificialperson","require","请输入法人名称"],
            ["artificialperson_id","require","请输入法人身份证"],
            ["sale_manage","require","请输入营销系统负责人"],
            ["sale_manage_phone","require","请输入营销系统负责人电话"],
            ["manbusiness","require","请输入公司主营业务"]
        ];
        $validate=new Validate($rule);
        $put=$request->put();
        if(!$validate->check($put)){
            return $this->resultArray($validate->getError(),"failed");
        }
        if(!Com::update($put,["id"=>$id])){
            return $this->resultArray('修改失败',"failed");
        }
        return $this->resultArray('修改成功');
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {

    }

    /**
     * 上传企业营业执照
     */
    public function uploadBusinessLicense()
    {
        $request=Request::instance();
        $business=$request->file("businesslicense");
        $path="/upload/company/businesslicense/";
        $info=$business->move(ROOT_PATH.'public'.$path);
        if($info){
            return $this->resultArray("上传成功",'',$path.$info->getSaveName());
        }
        return $this->resultArray('上传失败',"failed");
    }

    /**
     * 上传法人身份证
     */
    public function uploadArtificialPersonId()
    {
        $request=Request::instance();
        $artificial=$request->file('artificialpersonid');
        $path="/upload/company/artificialpersonid/";
        $info=$artificial->move(ROOT_PATH.'public'.$path);
        if($info){
            return $this->resultArray('上传成功','',$path.$info->getSaveName());
        }
        return $this->resultArray('上传失败',"failed");
    }

    /**
     * 上传商标
     * @return array
     */
    public function uploadTrademark()
    {
        $request=Request::instance();
        $trademark=$request->file("trademark");
        $path='/upload/company/trademark/';
        $info=$trademark->move(ROOT_PATH.'public'.$path);
        if($info){
            return $this->resultArray('上传成功!','',$path.$info->getSaveName());
        }
        return $this->resultArray('上传失败!',"failed");
    }

    /**
     * 验证企业填写的信息
     * @return array
     */
    public function verifyCompanyInfo()
    {
        $user = $this->getSessionUser();
        $node_id = $user["user_node_id"];
        $node=Node::get($node_id);
        if(!$node){
            return $this->resultArray('未获取到企业信息!',"failed");
        }
        $comInfo=(new Com)->field("name,industry_id,industry_name,tax_registration_number,business_license,artificialperson,artificialperson_id,sale_manage,sale_manage_phone,manbusiness")->where(["id"=>$node->com_id])->find();
        if(!$comInfo){
            return $this->resultArray('未获取到企业信息!',"failed");
        }
        $com_arr=collection($comInfo)->toArray();
        $key_arr=array_values($com_arr);
        if(in_array('',$key_arr)){
            return $this->resultArray('请先完善必填信息!',"failed");
        }
    }
}
