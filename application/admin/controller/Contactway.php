<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\common\controller\Common;
use think\Validate;

class Contactway extends Common
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $limits = $this->getLimit();
        $detail = $this->request->get('detail');
        $where = [];
        if (!empty($detail)) {
            $where['detail'] = $detail;
        }
        $user = $this->getSessionUser();
        $where["node_id"] = $user["user_node_id"];
        return $this->resultArray('', '', (new \app\admin\model\Contactway())->getAll($limits['limit'], $limits['rows'], $where));
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
            ['detail','require','请填写描述'],
        ];
        $validate = new Validate($rule);
        $data = $this->request->post();
        $data['html'] = serialize($data['html']);
        //dump($data);die;
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), 'failed');
        }
        $data["node_id"] = $this->getSessionUser()['user_node_id'];
        if (!\app\admin\model\Contactway::create($data)) {
            return $this->resultArray('添加失败', 'failed');
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
        $data = (new \app\admin\model\Contactway)->where(["id" => $id])->field("create_time,update_time", true)->find();
        $data['html'] = @unserialize($data['html']);
        if($data['html'] == false){
            $data['html'] = [];
        }
        return $this->resultArray('', '', $data);
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
            ['detail','require','请填写描述'],
        ];
        $validate = new Validate($rule);
        $data = $this->request->put();
        $data['html'] = serialize($data['html']);
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), 'failed');
        }
        return $this->publicUpdate((new \app\admin\model\Contactway),$data,$id);
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        return $this->deleteRecord((new \app\admin\model\Contactway),$id);
    }

    /**
     * 获取所有域名
     * @return array
     */
    public function getContactway()
    {
        $field="id,name as text";
        return $this->getList((new \app\admin\model\Contactway),$field);
    }
}
