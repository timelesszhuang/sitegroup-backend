<?php

namespace app\admin\controller;

use app\common\controller\Common;
use think\Request;
use app\admin\model\Scatteredarticletype as sca;
use think\Validate;

class Scatteredarticletype extends Common
{
    /**
     * 获取零散段落分类
     * @return array
     */
    public function getType()
    {
        $user = $this->getSessionUser();
        $where['node_id'] = $user['user_node_id'];
        $data=sca::field("id,name as text")->where($where)->order("id","desc")->select();
        return $this->resultArray('', '', $data);
    }

    /**
     * 零散分类表格数据
     * @return array
     */
    public function index()
    {
        $request = $this->getLimit();
        $name = $this->request->get('name');
        $where = [];
        if (!empty($name)) {
            $where["name"] = ["like", "%$name%"];
        }
        $user = $this->getSessionUser();
        $where["node_id"] = $user["user_node_id"];
        $data = (new sca())->getTypes($request["limit"], $request["rows"], $where);
        return $this->resultArray('', '', $data);
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $rule = [
            ['name', 'require', "请填写分类名"],
            ['name','unique:scatteredarticletype',"分类名重复"],
            ["detail", "require", "请填写描述"],
        ];
        $validate = new Validate($rule);
        $data = $this->request->post();
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), 'failed');
        }
        $data["node_id"] = $this->getSessionUser()['user_node_id'];

        if (!sca::create($data)) {
            return $this->resultArray('添加失败', 'failed');
        }
        return $this->resultArray('添加成功');
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request $request
     * @param  int $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        $rule = [
            ['name', 'require', "请填写分类名"],
            ['name','unique:scatteredarticletype',"分类名重复"],
            ["detail", "require", "请填写描述"],
        ];
        $data = $request->put();
        $validate = new Validate($rule);
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), 'failed');
        }
        if (!(new sca)->save($data, ["id" => $id])) {
            return $this->resultArray('修改失败', 'failed');
        }
        return $this->resultArray('修改成功');
    }

    /**
     * @param $id
     * @return array
     */
    public function read($id)
    {
        return $this->getread((new sca), $id);
    }

}
