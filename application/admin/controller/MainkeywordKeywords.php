<?php

namespace app\admin\controller;

use app\common\controller\Common;
use think\Request;
use \app\admin\model\MainkeywordKeywords as Main;
use think\Validate;
class MainkeywordKeywords extends Common
{
    protected $conn='';
    /**
     * 初始化操作
     */
    public function _initialize()
    {
        $this->conn=new Main();
    }
    /**
     * 获取
     *
     * @return \think\Response
     */
    public function index()
    {
        $request = $this->getLimit();
        $name= $this->request->get('mainkeyword_name');
        $where = [];
        if (!empty($name)) {
            $where["mainkeyword_name"] = ["like", "%$name%"];
        }
        $user = $this->getSessionUser();
        $where["node_id"] = $user["user_node_id"];
        $data = $this->conn->getType($request["limit"], $request["rows"], $where);
        return $this->resultArray('', '', $data);
    }



}
