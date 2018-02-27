<?php

namespace app\common\controller;

use think\Request;
use think\Validate;

class Contactway extends CommonLogin
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
        $user_info = $this->getSessionUserInfo();
        $where["node_id"] =$user_info["node_id"];
        return $this->resultArray('', '', (new \app\common\model\Contactway())->getAll($limits['limit'], $limits['rows'], $where));
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
            return $this->resultArray('failed',$validate->getError() );
        }
        $user_info = $this->getSessionUserInfo();
        $data["node_id"] = $user_info["node_id"];
        if (!\app\common\model\Contactway::create($data)) {
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
        $data = (new \app\common\model\Contactway)->where(["id" => $id])->field("create_time,update_time", true)->find();
        $data['html'] = unserialize($data['html']);
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
            return $this->resultArray('failed',$validate->getError());
        }
        return $this->publicUpdate((new \app\common\model\Contactway),$data,$id);
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        return $this->deleteRecord((new \app\common\model\Contactway),$id);
    }

    /**
     * 获取所有联系方式
     * @return array
     */
    public function getContactway()
    {
        $field="id,name as text";
        return $this->getList((new \app\common\model\Contactway),$field);
    }
}
