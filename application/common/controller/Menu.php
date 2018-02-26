<?php

namespace app\common\controller;

use app\common\controller\Common;
use think\Db;
use think\Validate;
use think\Request;

class Menu extends CommonLogin
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
        $user_info = $this->getSessionUserInfo();
        $where["node_id"] =$user_info["node_id"];
//        //需要组织成树的形式
        $menu = Db::name('menu')->where($where)->select();
        $tree = array();
        //创建基于主键的数组引用
        $refer = array();
        foreach ($menu as $key => $data) {
            unset($data['flag']);
            unset($data['type_id']);
            unset($data['detailtemplate']);
            unset($data['listtemplate']);
            unset($data['covertemplate']);
            $menu[$key] = $data;
            $refer[$data['id']] = &$menu[$key];
        }
        //循环中还需要设置下当前menu相关信息
        foreach ($menu as $key => $data) {
            // 判断是否存在parent
            $parentId = $data['p_id'];
            if ($parentId == 0) {
                //根节点元素
                $tree[] = &$menu[$key];
            } else {
                if (isset($refer[$parentId])) {
                    //当前正在遍历的父亲节点的数据
                    $parent = &$refer[$parentId];
                    //把当前正在遍历的数据赋值给父亲类的  children
                    $parent['child'][] = &$menu[$key];
                }
            }
        }
        return $this->resultArray('', '', $tree);
    }


    /**
     * @param $id
     * @return array
     */
    public function read($id)
    {
        $field = \app\common\model\Menu::where(["id" => $id])->field("create_time,update_time", true)->find();
        if ($field) {
            $field = $field->toArray();
            //近期要测试一个栏目选择多个栏目的功能
            $field['type_id'] = implode(",", array_filter(explode(",", $field['type_id'])));
            return $this->resultArray('', '', $field);
        }
        $this->resultArray('failed','获取失败');
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
    protected function check_unique($generate_name, $id = 0)
    {
        $where = [];
        $where['generate_name'] = $generate_name;
        $user_info = $this->getSessionUserInfo();
        $where["node_id"] =$user_info["node_id"];
        if ($id != 0) {
            $where["id"] = ['neq', $id];
        }
        $field = \app\common\model\Menu::where($where)->find();
        return $field ? false : true;
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
            ["covertemplate", '^.*\.html$', "封面模板格式错误"],
            ["listtemplate", '^.*\.html$', "列表模板格式错误"],
            ["detailtemplate", '^.*\.html$', "详情模板格式错误"],
            ["listsize", 'number', "列表数只能是数字"],
            ['generate_name', 'require|alphaNum', "请填写英文名称|英文名只能是英文或者数字"]
        ];
        if (intval($flag) > 1) {
        }
        $validate = new Validate($rule);
        $data = $this->request->post();
        $where = [];
        $user_info = $this->getSessionUserInfo();
        $where["node_id"] =$user_info["node_id"];
        if (!$this->check_unique($data['generate_name'])) {
            return $this->resultArray( 'failed',"英文名称已存在");
        };
        if (!$validate->check($data)) {
            return $this->resultArray('failed',$validate->getError());
        }
        if ($data['listsize'] == 0) {
            unset($data['listsize']);
        }
        $data["node_id"] = $user_info["node_id"];
        if (isset($data["type_id"])&&$data["type_id"]&&count($data["type_id"]) > 0) {
            $data["type_id"] = "," . implode(',', $data["type_id"]) . ",";
        } else {
            $data["type_id"] = "";
        }
        $pid = [];
        if (isset($data["p_id"]) && $data["p_id"] != 0) {
            $field = \app\common\model\Menu::where(["id" => $data["p_id"]])->find();
            if ($field && $field['p_id'] != 0) {
                $pid = array_filter(explode(",", $field['path']));
            }
            $pid[] = $data["p_id"];
            $data["path"] = "," . implode(',', $pid) . ",";
        }
        if (!\app\common\model\Menu::create($data)) {
            return $this->resultArray( 'failed','添加失败');
        }
        return $this->resultArray('添加成功');
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request $request
     * @param  int $id
     * @return \think\Response
     * @author jingzheng
     */
    public function update(Request $request, $id)
    {
        $flag = $request->post('flag');
        $rule = [
            ['name', 'require', "请填写菜单"],
            ["flag", "require", "请选择栏目类型"],
            ["flag_name", "require", "请选择栏目类型"],
            ["tag_id", "require", "请填写分类"],
            ["tag_name", 'require', "请填写分类"],
            ["covertemplate", '^.*\.html$', "封面模板格式错误"],
            ["listtemplate", '^.*\.html$', "列表模板格式错误"],
            ["detailtemplate", '^.*\.html$', "详情模板格式错误"],
            ["listsize", 'number', "列表数只能是数字"],
            ['generate_name', 'require|alphaNum', "请填写英文名称|英文名只能是英文或者数字"]
        ];
        if (intval($flag) > 1) {
        }
        $validate = new Validate($rule);
        $data = $this->request->post();
        if (!$this->check_unique($data['generate_name'], $data['id'])) {
            return $this->resultArray( 'failed',"英文名称已存在");
        };
        if (isset($data["type_id"])&&count($data["type_id"]) > 0) {
            $data["type_id"] = "," . implode(',', $data["type_id"]) . ",";
        } else {
            $data["type_id"] = "";
        }
        $pid = [];
        if ($data["p_id"] != 0) {
            $field = \app\common\model\Menu::where(["id" => $data["p_id"]])->find();
            if ($field && $field['p_id'] != 0) {
                $pid = array_filter(explode(",", $field['path']));
            }
            $pid[] = $data["p_id"];
            $data["path"] = "," . implode(',', $pid) . ",";
        }
        if (!$validate->check($data)) {
            return $this->resultArray('failed',$validate->getError());
        }
        if ($data['listsize'] == 0) {
            unset($data['listsize']);
        }
        return $this->publicUpdate((new \app\common\model\Menu), $data, $id);
    }

    /**
     * 删除指定资源
     * @param  int $id
     * @return array
     * @author jingzheng
     */
    public function delete($id)
    {
        return $this->deleteRecord((new \app\common\model\Menu), $id);
    }

    /**
     * 获取所有栏目
     * @return array
     */
    public function getMenu()
    {
        $field = "id,name as text,flag_name,title,type_name,tag_name";
        $user_info = $this->getSessionUserInfo();
        $where["node_id"] =$user_info["node_id"];
        $where['p_id'] = 0;
        $data = (new \app\common\model\Menu())->getlist($where, $field);
        return $this->resultArray('', '', $data);
    }

    /**
     * 根据flag获取菜单列表
     * @param Request $request
     * @return mixed
     * @author sunjingyang
     */
    public function getUpMenu(Request $request, $flag, $id = 0)
    {
        $field = "id,name as text,flag_name,title,type_name,tag_name,path,p_id";
        $user_info = $this->getSessionUserInfo();
        $where["node_id"] =$user_info["node_id"];
        $where["flag"] = $flag;
        $id != 0 && $where["id"] = ['neq', $id];
        $data = (new \app\common\model\Menu())->getlist($where, $field);
        $list = [];
        $data_key = [];
        foreach ($data as $menu) {
            $data_key[$menu['p_id']][] = $menu;
        }
        foreach ($data as $menu) {
            if ($menu['p_id'] == 0) {
                $menu['text'] = $menu['text'] . '[' . $menu['tag_name'] . ']';
                $list[] = $menu;
                if (isset($data_key[$menu['id']])) foreach ($data_key[$menu['id']] as $item) {
                    $item['text'] = "|-" . $item['text'] . '[' . $item['tag_name'] . ']';
                    $list[] = $item;
                }
            }
        }
        return $this->resultArray('', '', $list);
    }

    /**
     * 排序
     */
    public function sort(Request $request,$id){
        $data = $this->request->post();
        $menu = new \app\common\model\Menu();
        $menudata =  $menu->where('id', $id)
            ->update([
                'sort' => $data['sort']
            ]);
        if($menudata){
            return $this->resultArray('修改成功');
        }else{
            return $this->resultArray('failed','修改失败' );
        }
    }
}
