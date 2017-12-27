<?php

namespace app\admin\controller;

use app\common\controller\Common;
use app\common\traits\Osstrait;
use think\Validate;
use think\Request;
use app\common\traits\Obtrait;
use app\admin\model\TypeTag as Type_Tag;

class TypeTag extends Common
{
    use Obtrait;
    use Osstrait;

    /**
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index(Request $request)
    {
        $request = $this->getLimit();
        $tag = $this->request->get('tag');
        $all = $this->request->get('all');
        if($tag){
            $where["tag"] = ["like", "%$tag%"];
        }
        $user = $this->getSessionUser();
        $where["node_id"] = $user["user_node_id"];
        $data = (new Type_Tag)->getList($request["limit"], $request["rows"], $where);
        if($all){
            $data = (new Type_Tag)->where($where)->select();
        }
        return $this->resultArray('', '', $data);
    }

    /**
     * @param $id
     * @return array
     */
    public function read($id)
    {
        return $this->getread((new Type_Tag), $id);
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request $request
     * @return array
     */
    public function save(Request $request)
    {
        $rule = [
            ["tag", "require|unique:type_tag,node_id", "请输入分类名称|标签重复"],
        ];
        $validate = new Validate($rule);
        $data = $request->post();
        $user = $this->getSessionUser();
        $data['node_id'] = $user['user_node_id'];
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), "failed");
        }
        if (!Type_Tag::create($data)) {
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
        if (!(new Type_Tag)->save($data, ["id" => $id])) {
            return $this->resultArray('修改失败', 'failed');
        }
        return $this->resultArray("修改成功");
    }

    /**
     * @param Request $request
     * @param $id
     * @return array
     */
    public function delete(Request $request, $id)
    {
        return $this->deleteRecord((new Type_Tag),$id);
    }
}
