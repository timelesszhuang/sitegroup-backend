<?php

namespace app\common\controller;

use app\common\traits\Osstrait;
use think\Validate;
use think\Request;
use app\common\traits\Obtrait;
use app\common\model\TypeTag as this_model;

class TypeTag extends CommonLogin
{
    use Obtrait;
    use Osstrait;

    /**
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $request = $this->getLimit();
        $tag = $this->request->get('tag');
        $all = $this->request->get('all');
        if($tag){
            $where["tag"] = ["like", "%$tag%"];
        }
        $user = $this->getSessionUserInfo();
        $where["node_id"] = $user["node_id"];
        $data = (new this_model)->getList($request["limit"], $request["rows"], $where);
        if($all){
            $data = (new this_model)->where($where)->select();
        }
        return $this->resultArray($data);
    }

    /**
     * @param $id
     * @return array
     */
    //TODO oldfunction
    public function read($id)
    {
        return $this->getread((new this_model), $id);
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request $request
     * @return array
     */
    //TODO oldfunction
    public function save(Request $request)
    {
        $rule = [
            ["tag", "require|unique:type_tag,node_id", "请输入分类名称|标签重复"],
        ];
        $validate = new Validate($rule);
        $data = $request->post();
        $user = $this->getSessionUserInfo();
        $data['node_id'] = $user['node_id'];
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), "failed");
        }
        if (!this_model::create($data)) {
            return $this->resultArray("添加失败", "failed");
        }
        return $this->resultArray("添加成功");
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request $request
     * @param  int $id
     * @return array
     */
    public function update(Request $request, $id)
    {
        $rule = [
            ["tag", "require|unique:type_tag,node_id", "请输入分类名称|标签重复"],
        ];
        $data = $request->put();
        $validate = new Validate($rule);
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), 'failed');
        }
        if (!(new this_model)->save($data, ["id" => $id])) {
            return $this->resultArray('修改失败', 'failed');
        }
        return $this->resultArray('success',"修改成功");
    }

    /**
     * @param Request $request
     * @param $id
     * @return array
     */
    //TODO oldfunction
    public function delete(Request $request, $id)
    {
        return $this->deleteRecord((new this_model),$id);
    }
}
