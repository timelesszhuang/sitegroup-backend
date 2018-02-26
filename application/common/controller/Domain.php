<?php

namespace app\common\controller;

use think\Request;
use think\Validate;
use app\common\controller\Common;

class Domain extends CommonLogin
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $limits = $this->getLimit();
        $domain = $this->request->get('domain');
        $where = [];
        if (!empty($domain)) {
            $where['domain'] = ["like", "%$domain%"];
        }
        $user_info = $this->getSessionUserInfo();
        $where["node_id"] =$user_info["node_id"];
        return $this->resultArray('', '', (new \app\common\model\Domain())->getAll($limits['limit'], $limits['rows'], $where));
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
        $rule = [
//            ['registrant_user', "require", "请填写注册人"],
//            ['registrant_tel', 'require', "请填写手机号"],
//            ["registrant_email","require","请填写邮箱"],
            ["domain","require","请填写域名"]
        ];
        $validate = new Validate($rule);
        $data = $this->request->post();
        if (!$validate->check($data)) {
            return $this->resultArray('failed',$validate->getError());
        }
        $user_info = $this->getSessionUserInfo();
        $data["node_id"] = $user_info["node_id"];
        if (!\app\common\model\Domain::create($data)) {
            return $this->resultArray('failed','添加失败' );
        }
        return $this->resultArray('添加成功');
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        return $this->getread((new \app\common\model\Domain),$id);
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
        $rule = [
            ["domain","require","请填写域名"]
        ];
        $validate = new Validate($rule);
        $data = $this->request->put();
        if (!$validate->check($data)) {
            return $this->resultArray('failed',$validate->getError());
        }
        return $this->publicUpdate((new \app\common\model\Domain),$data,$id);
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        return $this->deleteRecord((new \app\common\model\Domain),$id);
    }

    /**
     * 获取所有域名
     * @return array
     */
    public function getDomain()
    {
        $field="id,domain as text";
        return $this->getList((new \app\common\model\Domain),$field);
    }

    /**
     * 获取办公地点
     * @return array
     */
    public function getOffice()
    {
        $office=[
            ["id"=>1,"text"=>"阿里云"],
            ["id"=>2,"text"=>"新网互联"],
            ["id"=>3,"text"=>"百度"],
            ["id"=>4,"text"=>"蜂巢"]
        ];
        return $this->resultArray('','',$office);
    }
}
