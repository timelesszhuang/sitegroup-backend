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
            $field['type_id'] = array_filter(explode(",",$field['type_id']));
            return $this->resultArray('', '', $field);
        }
        $this->resultArray('获取失败', 'failed', []);
    }

    /**
     * 验证英文名唯一性
     * @param $generate_name
     * @param $flag
     * @param $node_id
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author sunjingyang
     */
    public function check_unique($generate_name,$flag,$node_id){
        $where=[];
        $where['generate_name']=$generate_name;
        $where['flag']=$flag;
        $where['node_id']=$node_id;
        $field = \app\admin\model\Menu::where($where)->find();
        return $field?false:true;
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
        $flag = $request->post('flag');
        $rule = [
            ['name', 'require', "请填写菜单"],
            ["flag", "require", "请选择栏目类型"],
            ["flag_name", "require", "请选择栏目类型"],
            ["tag_id", "require", "请填写分类"],
            ["tag_name", 'require', "请填写分类"],
            //['generate_name','require|unique:menu,flag^node_id']
        ];
        if (intval($flag) > 1) {
            array_push($rule, ["type_id", "require", "请选择分类id"]);
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
        $data["type_id"] = ",".implode(',',$data["type_id"]).",";
        $pid=[];
        if($data["p_id"]!=0){
            $field = \app\admin\model\Menu::where(["id" => $data["p_id"]])->find();
            if($field && $field['p_id']!=0){
                $pid[]=$field['p_id'];
            }
            $pid[]=$data["p_id"];
            $data["path"] = ",".implode(',',$pid).",";
        }
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
        }
        $validate = new Validate($rule);
        $data = $this->request->post();
        $data["type_id"] = ",".implode(',',$data["type_id"]).",";
        $pid=[];
        if($data["p_id"]!=0){
            $field = \app\admin\model\Menu::where(["id" => $data["p_id"]])->find();
            if($field && $field['p_id']!=0){
                $pid[]=$field['p_id'];
            }
            $pid[]=$data["p_id"];
            $data["path"] = ",".implode(',',$pid).",";
        }
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), 'failed');
        }
        return $this->publicUpdate((new \app\admin\model\Menu), $data, $id);
    }

    /**
     * 删除指定资源
     * @param  int $id
     * @return array
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

    /**
     * 根据flag获取菜单列表
     * @param Request $request
     * @return mixed
     * @author sunjingyang
     */
    public function getUpMenu(Request $request){
        $flag = $request->get('flag');
        $field = "id,name as text,flag_name,title,type_name,tag_name,path,p_id";
        $user = $this->getSessionUser();
        $where["node_id"] = $user["user_node_id"];
        $where["flag"] = $flag;
        $data = (new \app\admin\model\Menu())->getlist($where, $field);
        $list = [];
        $data_key=[];
        foreach ($data as $menu){
            $data_key[$menu['p_id']][]=$menu;
        }
        foreach ($data as $menu){
            if($menu['p_id']==0){
                $menu['text']=$menu['text'].'['.$menu['tag_name'].']';
                $list[]=$menu;
                foreach($data_key[$menu['id']] as $item){
                    $item['text']="|-".$item['text'].'['.$item['tag_name'].']';
                    $list[]=$item;
                }
            }
        }
        return $this->resultArray('', '', $list);
    }
}
