<?php

namespace app\admin\controller;

use app\common\controller\Common;
use think\Request;
use app\common\model\Sohunews;

class Souhu extends Common
{
    protected $conn='';

    /**
     * 初始化操作
     */
    public function _initialize()
    {
        $this->conn=new Sohunews();
    }

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $request = $this->getLimit();
        $title= $this->request->get('title');
        $type_id= $this->request->get('type_id');
        $where = [];
        if (!empty($title)) {
            $where["title"] = ["like", "%$title%"];
        }
        if(!empty($type_id)){
            $where["type_id"]=$type_id;
        }
        $data = $this->conn->getArticle($request["limit"], $request["rows"], $where);
        return $this->resultArray('', '', $data);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        return $this->resultArray('','',$this->conn->getOne($id));
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }

    /**
     * 通过id获取分类
     * @param $id
     * @return mixed|string
     */
    public function typeList()
    {
        $arr=[
            ["id"=>1,"text"=>"科技类"],
            ["id"=>2,"text"=>"教育类"],
            ["id"=>3,"text"=>"财经类"],
            ["id"=>4,"text"=>"美食类"],
            ["id"=>5,"text"=>"社会类"]
        ];
        return $this->resultArray("","",$arr);
    }
}
