<?php

namespace app\common\controller;


use \app\common\model\MainkeywordKeywords as Main;
class MainkeywordKeywords extends CommonLogin
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
        $name= $this->request->get('mainkeyword_id');
        $where = [];
        if (!empty($name)) {
            $where["mainkeyword_id"] = ["like", "%$name%"];
        }
        $user_info = $this->getSessionUserInfo();
        $where["node_id"] =$user_info["node_id"];
        $data = $this->conn->getType($request["limit"], $request["rows"], $where);
        return $this->resultArray('', '', $data);
    }



}
