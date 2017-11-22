<?php

namespace app\admin\controller;

use app\common\controller\Common;
use think\Validate;
use think\Request;

class Menu extends Common
{
    /**
     * @return array
     */
    public function index()
    {
        $request = $this->getLimit();
        $name = $this->request->get('name');
        $flag = $this->request->get('flag');
        $tag_id = $this->request->get('tag_id');
        $where = [];
        if (!empty($name)) {
            $where["name"] = ["like", "%$name%"];
        }
        if (!empty($flag)) {
            $where['flag'] = $flag;
        }
        if (!empty($tag_id)) {
            $where["tag_id"] = $tag_id;
        }
        $user = $this->getSessionUser();
        $where["node_id"] = $user["user_node_id"];
        $data = (new \app\admin\model\Menu())->getMenu($request["limit"], $request["rows"], $where);
        return $this->resultArray('', '', $data);
    }

    /**
     * @param $id
     * @return array
     */
    public function read($id)
    {
        $field = \app\admin\model\Menu::where(["id" => $id])->field("create_time,update_time", true)->find();
        if ($field) {
            $field = $field->toArray();
            //近期要测试一个栏目选择多个栏目的功能
            $field['type_id'] = intval($field['type_id']);
            return $this->resultArray('', '', $field);
        }
        $this->resultArray('获取失败', 'failed', []);
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $flag = $request->post('flag');
        $rule = [
            ['name', 'require', "请填写菜单"],
            ["flag", "require", "请选择栏目类型"],
            ["flag_name", "require", "请选择栏目类型"],
            ["tag_id", "require", "请填写分类"],
            ["tag_name", 'require', "请填写分类"]
        ];
        if (intval($flag) > 1) {
            array_push($rule, ["type_id", "require", "请选择分类id"]);
            array_push($rule, ["type_name", "require", "请选择分类名称"]);
        }
        $validate = new Validate($rule);
        $data = $this->request->post();
        $where = [];
        $user = $this->getSessionUser();
        $where["node_id"] = $user["user_node_id"];
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), 'failed');
        }
        $data["node_id"] = $this->getSessionUser()['user_node_id'];
        if (!\app\admin\model\Menu::create($data)) {
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
     * @author guozhen
     */
    public function update(Request $request, $id)
    {
        $flag = $request->post('flag');
        $rule = [
            ['name', 'require', "请填写菜单"],
            ["flag", "require", "请选择栏目类型"],
            ["flag_name", "require", "请选择栏目类型"],
            ["tag_id", "require", "请填写分类"],
            ["tag_name", 'require', "请填写分类"]
        ];
        if (intval($flag) > 1) {
            array_push($rule, ["type_id", "require", "请选择分类id"]);
            array_push($rule, ["type_name", "require", "请选择分类名称"]);
        }
        $validate = new Validate($rule);
        $data = $this->request->post();
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), 'failed');
        }
        return $this->publicUpdate((new \app\admin\model\Menu), $data, $id);
    }

    /**
     * 删除指定资源
     * @param  int $id
     * @return \think\Response
     * @author guozhen
     */
    public function delete($id)
    {
        return $this->deleteRecord((new \app\admin\model\Menu), $id);
    }

    /**
     * 获取所有栏目
     * @return array
     */
    public function getMenu()
    {
        $field = "id,name as text,flag_name,title,type_name,tag_name";
        $user = $this->getSessionUser();
        $where["node_id"] = $user["user_node_id"];
        $data = (new \app\admin\model\Menu())->getlist($where, $field);
        return $this->resultArray('', '', $data);
    }
}
