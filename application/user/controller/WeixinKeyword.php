<?php

namespace app\user\controller;

use app\common\controller\Common;
use think\Request;
class WeixinKeyword extends Common
{
    protected $conn='';
    /**
     * 初始化操作
     */
    public function _initialize()
    {
        $this->conn=new \app\admin\model\WeixinKeyword();
    }
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $request = $this->getLimit();
        $name= $this->request->get('name');
        $where = [];
        if (!empty($name)) {
            $where["name"] = ["like", "%$name%"];
        }
        $data = $this->conn->getKeyword($request["limit"], $request["rows"], $where);
        return $this->resultArray('', '', $data);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        $name= $this->request->get('name');
        if(empty($name)){
            return;
        }
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
    public function save()
    {
        $name= $this->request->get('name');
        $id= $this->request->get('id');
        if(empty($name) || empty($id)){
            return;
        }
        if($this->conn->editKeyword($id,$name)){
            return $this->resultArray('修改成功');
        }
        return $this->resultArray('修改失败', 'failed');
    }

    /**
     * 获取列表
     * @return array
     */
    public function getKeyList()
    {
        return $this->resultArray('','',$this->conn->getlist());
    }
}
