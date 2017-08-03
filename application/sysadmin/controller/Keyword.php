<?php

namespace app\sysadmin\controller;

use app\common\controller\Common;
use think\Request;
use \app\admin\model\WeixinKeyword as Scrapy;
use think\Validate;

class Keyword extends Common
{
    protected $conn='';
    /**
     * 初始化操作
     */
    public function _initialize()
    {
        $this->conn=new Scrapy();
    }

    /**
     * 获取所有关键词
     *
     * @return \think\Response
     */
    public function index()
    {
        $request = $this->getLimit();
        $id=$this->request->get('keyword_typename');
        $name= $this->request->get('name');
//        $typeid = $this->$request->get('');
        $where = [];
        if (!empty($name)) {
            $where["name"] = ["like", "%$name%"];
        }
        if (!empty($id)) {
            $where["type_id"] = $id;
        }
        $data = $this->conn->getKeyword($request["limit"], $request["rows"], $where);
        return $this->resultArray('', '', $data);
    }

    /**
     * 未授权爬取
     * @param $id
     * @return array
     */
    public function stopStatus($id)
    {
        return $this->resultArray('','',$this->conn->stopStatus($id));
    }

    /**
     * 已授权关键词爬取
     * @param $id
     * @return array
     */
    public function startStatus($id)
    {
        return $this->resultArray('','',$this->conn->startStatus($id));
    }

    /**
     * 启用爬取
     * @param $id
     * @return array
     */
    public function startScrapy($id)
    {
        return $this->resultArray('','',$this->conn->startScrapy($id));
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
     * 添加关键词
     */
    public function addKeyword()
    {
        $rule = [
            ['name', 'require', "请填关键词"],
            ["detail", "require", "请填详情"],
            ["type_name", "require", "请填关键词分类"],
        ];
        $validate = new Validate($rule);
        $data = $this->request->post();
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), "failed");
        }
        if (!Scrapy::create($data)) {
            return $this->resultArray("添加失败", "failed");
        }
        return $this->resultArray("添加成功");
    }
    /**
     * 获取一条数据
     */
    public function read($id)
    {
        return $this->resultArray('','',$this->conn->getOne($id));

    }
    /**
     * 修改关键词分类
     */
    public function editKeyword()
    { $rule = [
        ['name', 'require', "请填关键词"],
        ["detail", "require", "请填详情"],
        ["type_name", "require", "请填关键词分类"],
    ];
        $validate = new Validate($rule);
        $data = $this->request->post();
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), "failed");
        }
        if (!Scrapy::update($data)) {
            return $this->resultArray("修改失败", "failed");
        }
        return $this->resultArray("修改成功");
    }


}
