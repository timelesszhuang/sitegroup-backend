<?php

namespace app\sysadmin\controller;

use app\common\controller\Common;
use think\Request;
use \app\admin\model\WeixinKeyword as Scrapy;
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
        $name= $this->request->get('name');
        $where = [];
        if (!empty($name)) {
            $where["name"] = ["like", "%$name%"];
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



}
