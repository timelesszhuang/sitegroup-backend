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
        $id = $this->request->get('id');
        $where=[];
        if(!empty($name)){
            $where["name"] = ["like", "%$name%"];
        }
        if(!empty($id)){
            $where["id"]=$id;
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
        $data = (new \app\admin\model\Article())->field('id,title,content,articletype_id')->find($id);
        return $this->resultArray('','',$data);
    }


}
