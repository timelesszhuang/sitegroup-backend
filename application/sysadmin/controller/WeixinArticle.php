<?php

namespace app\sysadmin\controller;

use think\Controller;
use think\Request;
use app\common\controller\Common;
use \app\admin\model\WeixinArticle as Weixin;
class WeixinArticle extends Common
{
    protected $conn='';
    /**
     * 初始化操作
     */
    public function _initialize()
    {
        $this->conn=new Weixin();
    }

    /**
     * 获取微信采集文章列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $request = $this->getLimit();
        $title= $this->request->get('title');
        $where = [];
        if (!empty($title)) {
            $where["title"] = ["like", "%$title%"];
        }
        $data = $this->conn->getArticle($request["limit"], $request["rows"], $where);
        return $this->resultArray('', '', $data);
    }

}
