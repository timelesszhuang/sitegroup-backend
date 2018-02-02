<?php

namespace appcp\admin\controller;

use think\Controller;
use think\Request;
use app\common\controller\Common;
use app\admin\model\WangyiArticle as Wangyi;
use think\Validate;

class WangyiArticle extends Common
{
    protected $conn='';
    /**
     * 初始化操作
     */
//TODO oldfunction
    public function _initialize()
    {
        $this->conn=new Wangyi();
    }

    /**
     * 获取所有网易爬虫文章
     *
     * @return \think\Response
     */
//TODO oldfunction
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
     * 获取某个文章
     * @param $id
     * @return array
     */
//TODO oldfunction
    public function read($id)
    {
        return $this->resultArray('','',$this->conn->getOne($id));
    }

    /**
     * 获取所有分类
     * @return array
     */
//TODO oldfunction
    public function getTypes()
    {
        return $this->resultArray('','',$this->conn->allTypes());
    }
}
