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
        $request=$this->getLimit();
        $name = $this->request->get('name');
        $where=[];
        if(!empty($name)){
            $where["name"] = ["like", "%$name%"];
        }
        $user=(new Common())->getSessionUser();
        $where["node_id"]=$user["user_node_id"];
        $data = (new \app\admin\model\Menu())->getMenu($request["limit"], $request["rows"], $where);
        return $this->resultArray('', '', $data);
    }

    /**
     * @param $id
     * @return array
     */
    public function read($id)
    {
        return $this->getread((new \app\admin\model\Menu()),$id);
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
            ['name', 'require', "请填写菜单"],
            ["type","require","请选择栏目类型"],
            ["type_id","require","请选择分类id"],
            ["type_name","require","请选择分类名称"]
        ];
        $validate = new Validate($rule);
        $data = $this->request->post();
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), 'faile');
        }
        $data["node_id"] = $this->getSessionUser()['user_node_id'];
        if (!\app\admin\model\ScatteredTitle::create($data)) {
            return $this->resultArray('添加失败', 'faile');
        }
        return $this->resultArray('添加成功');
    }

}
