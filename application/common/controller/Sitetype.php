<?php

namespace app\common\controller;

use think\Controller;
use think\Request;
use app\common\controller\Common;
use think\Validate;

class Sitetype extends Common
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $limits = $this->getLimit();
        $name = $this->request->get('name');
        $where = [];
        if (!empty($name)) {
            $where['name'] = ["like","%$name%"];
        }
        $user_info = $this->getSessionUserInfo();
        $where["node_id"] = $user_info["node_id"];
        return $this->resultArray('', '', (new \app\common\model\SiteType())->getAll($limits['limit'], $limits['rows'], $where));
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
            ['name','require','请填写站点分类名'],
            ['chain_type','require','请填写链轮类型'],
        ];
        $validate = new Validate($rule);
        $data = $this->request->post();
        if (!$validate->check($data)) {
            return $this->resultArray('failed',$validate->getError());
        }
        $user_info = $this->getSessionUserInfo();
        $data["node_id"] = $user_info["node_id"];
        if (!\app\common\model\SiteType::create($data)) {
            return $this->resultArray( 'failed','添加失败');
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
        return $this->getread((new \app\common\model\SiteType),$id);
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
        $rule = [
            ['name','require','请填写站点分类名'],

            ['chain_type','require','请填写链轮类型'],
        ];
        $validate = new Validate($rule);
        $data = $this->request->put();
        if (!$validate->check($data)) {
            return $this->resultArray('failed',$validate->getError());
        }
        return $this->publicUpdate((new \app\common\model\SiteType),$data,$id);
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
     * 获取所有网站类型
     * @return array
     */
    public function getSiteType()
    {
        $field="id,name as text";
        return $this->getList((new \app\common\model\SiteType),$field);
    }



}