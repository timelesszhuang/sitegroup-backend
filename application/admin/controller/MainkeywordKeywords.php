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
     * 获取关键字
     * @return \think\Response
     */
    public function index()
    {
        $request = $this->getLimit();
        $time = $this->request->get('time');
        $name= $this->request->get('mainkeyword_name');
        $where = [];
        if (!empty($name)) {
            $where["mainkeyword_name"] = ["like", "%$name%"];
        }
        if (!empty($param["time"])) {
            $starttime = strtotime($time);
            $stoptime = starttime($time)-86400;
        }
        else {
            $starttime = time() - 86400;
            $stoptime = time();
        }
        $where['create_time'] = ['between', [$starttime, $stoptime]];
        $user = $this->getSessionUser();
        $where["node_id"] = $user["user_node_id"];
        $data = $this->conn->getType($request["limit"], $request["rows"], $where);
        return $this->resultArray('', '', $data);
    }



}
