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

    /**
     * 添加操作
     *
     * @return \think\Response
     */
    public function create($name)
    {
        if($this->conn->addKeyword($name)){
            return $this->resultArray('添加成功');
        }
        return $this->resultArray('添加失败', 'failed');
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save($id,$name)
    {
        if($this->conn->editKeyword($id,$name)){
            return $this->resultArray('修改成功');
        }
        return $this->resultArray('修改失败', 'failed');
    }

    /**
     * 获取一条数据
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        return $this->resultArray('','',$this->conn->getOne($id));
    }

    /**
     * 停止爬取
     * @param $id
     * @return array
     */
    public function stopScrapy($id)
    {
        return $this->resultArray('','',$this->conn->stopScrapy($id));
    }

    /**
     * 获取列表
     * @return array
     */
    public function getKeyList()
    {
        return $this->resultArray('','',$this->conn->getKeyList());
    }

}