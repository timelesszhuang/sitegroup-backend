<?php

namespace app\admin\controller;

use app\common\controller\Common;
use think\Request;
use \app\admin\model\MainkeywordSearchengineorder as Search;
use think\Validate;
class MainkeywordSearch extends Common
{
    protected $conn='';
    /**
     * 初始化操作
     */
    public function _initialize()
    {
        $this->conn=new Search();
    }
    /**
     * 获取所有数据
     *
     * @return \think\Response
     */
    public function index()
    {
        $request = $this->getLimit();
        $mainkeyword_id = $this->request->get('mainkeyword_id');
        $time = $this->request->get('time');
        $url = $this->request->get('url');
        $where = [];
        if (!empty($mainkeyword_id)) {
            $where["mainkeyword_id"] = $mainkeyword_id;
        }
        if (!empty($time)) {
            $starttime = strtotime($time);
            $stoptime = strtotime($time)+86400;
        }
        else {
            $starttime = time() - 86400;
            $stoptime = time();
        }

        $user = $this->getSessionUser();
        $where['create_time'] = ['between', [$starttime, $stoptime]];
        $where["node_id"] = $user["user_node_id"];
        $data = $this->conn->getType($request["limit"], $request["rows"], $where,$url);
        return $this->resultArray('', '', $data);
    }
   public function mainkeyword(){
       $data = (new \app\admin\model\Keyword())->keyword();
       return $this->resultArray('','',$data);

   }


}
