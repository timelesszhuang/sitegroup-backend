<?php

namespace app\common\controller;

use think\Request;
use think\Validate;

class Code extends CommonLogin
{
    /**
     * 显示资源列表
     * @author jingzheng
     * @return \think\Response
     */
    public function index()
    {
        $limits = $this->getLimit();
        $name = $this->request->get('name');
        $where = [];
        if (!empty($name)) {
            $where['name'] = ["like", "%$name%"];
        }
        $user_info = $this->getSessionUserInfo();
        $where["node_id"] =$user_info["node_id"];
        return $this->resultArray('', '', (new \app\common\model\Code())->getAll($limits['limit'], $limits['rows'], $where));
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
            ['name', 'require', "请填用途"],
            ["code", "require", "请填代码"],
        ];
        $validate = new Validate($rule);
        $data = $this->request->post();
        if (!$validate->check($data)) {
            return $this->resultArray('failed',$validate->getError());
        }
        $user_info = $this->getSessionUserInfo();
        $data["node_id"] =$user_info["node_id"];
        if (!\app\common\model\Code::create($data)) {
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
        return $this->getread((new \app\common\model\Code), $id);
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
            ['name', 'require', "请填用途"],
            ["code", "require", "请填代码"]
        ];
        $validate = new Validate($rule);
        $data = $this->request->put();
        if (!$validate->check($data)) {
            return $this->resultArray('failed',$validate->getError());
        }
        return $this->publicUpdate((new \app\common\model\Code),$data,$id);
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        return $this->deleteRecord((new \app\common\model\Code),$id);
    }

    /**
     * 获取所有code
     * @return array
     */
    public function getCodes()
    {
        $field="id,name as text";
        return $this->getList((new \app\common\model\Code),$field);
    }
}
