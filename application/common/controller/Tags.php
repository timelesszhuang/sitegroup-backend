<?php

namespace app\common\controller;

use app\common\exception\ProcessException;
use app\common\traits\Osstrait;
use think\Validate;
use think\Request;
use app\common\traits\Obtrait;
use app\common\model\Tags as this_model;

class Tags extends CommonLogin
{
    use Obtrait;
    use Osstrait;

    /**
     * @param Request $request
     * @param this_model $this_model
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    //TODO oldfunction
    public function index(Request $request, this_model $this_model)
    {
        $request = $this->getLimit();
        $tag = $this->request->get('tag');
        $all = $this->request->get('all');
        if ($tag) {
            $where["tag"] = ["like", "%$tag%"];
        }
        $user = $this->getSessionUser();
        $where["node_id"] = $user["user_node_id"];
        $data = $this_model->getList($request["limit"], $request["rows"], $where);
        if ($all) {
            $data = $this_model->where($where)->select();
        }
        return $this->resultArray('', '', $data);
    }

    /**
     * @param $id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    //TODO oldfunction
    public function read($id)
    {
        return $this->getread((new this_model), $id);
    }

    /**
     * @param Request $request
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getTagList(Request $request)
    {
        $data = $request->get();
        $user = $this->getSessionUserInfo();
        if (isset($data['type']) && $data['type']) {
            $where['type'] = $data['type'];
        }
        $where['node_id'] = $user['node_id'];
        $datas = (new this_model)->where($where)->select();
        $datass = [];
        foreach ($datas as $value) {
            $datass[$value['type']][$value['id']] = $value['name'];
        }
        if (isset($data['type']) && $data['type']) {
            if (isset($datass[$data['type']])) {
                $datass = $datass[$data['type']];
            }
        }
        return $this->resultArray($datass);
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request $request
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function save(Request $request)
    {
        try {
            $rule = [
                ["name", "require", "请输入分类名称"],
                ['type', "require", "请输入分类类型"]
            ];
            $validate = new Validate($rule);
            $data = $request->post();
            $user = $this->getSessionUserInfo();
            $data['node_id'] = $user['node_id'];
            if (!$validate->check($data)) {
                Common::processException($validate->getError());
            }
            $dataa = (new this_model)->where($data)->find();
            if (!$dataa) {
                if (!this_model::create($data)) {
                    Common::processException("添加失败");
                }
                $id = (new this_model)->getLastInsID();
            } else {
                $id = $dataa['id'];
            }
            $where['type'] = $data['type'];
            $where['node_id'] = $user['node_id'];
            $datas = (new this_model)->where($where)->select();
            $datass = [];
            foreach ($datas as $value) {
                $datass[$value['id']] = $value['name'];
            }
        } catch (ProcessException $exception) {
            return $this->resultArray('failed', "添加失败");
        }
        return $this->resultArray('success', "添加成功", ['id' => (int)$id, 'data' => $datass]);
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request $request
     * @param  int $id
     * @return array
     */
    //TODO oldfunction
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
        return $this->resultArray("修改成功");
    }

    /**
     * @param Request $request
     * @param $id
     * @return array
     */
    //TODO oldfunction
    public function delete(Request $request, $id)
    {
        return $this->deleteRecord((new this_model), $id);
    }
}
