<?php

namespace app\common\controller;

use app\common\controller\Common;
use think\Request;
use \app\common\model\MainkeywordSearchengineorder as Search;
use think\Validate;
class MainkeywordSearch extends CommonLogin
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
        if (empty($time)) {
            $starttime = strtotime(date('Y-m-d',time()))-86400;
            $stoptime = strtotime(date('Y-m-d',time()));
        }
        else {
            $starttime = strtotime($time);
            $stoptime = strtotime($time)+86400;

        }
        $user_info = $this->getSessionUserInfo();
        $where['create_time'] = ['between', [$starttime, $stoptime]];
        $where["node_id"] =$user_info["node_id"];
        $data = $this->conn->getType($request["limit"], $request["rows"], $where,$url);
        return $this->resultArray('', '', $data);
    }
   public function mainkeyword(){
       $data = (new \app\common\model\Keyword())->keyword();
       return $this->resultArray('','',$data);

   }


}
