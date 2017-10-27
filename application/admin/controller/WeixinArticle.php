<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\common\controller\Common;
use app\admin\model\WeixinArticle as Weixin;
use think\Validate;

class WeixinArticle extends Common
{
    protected $conn = '';

    /**
     * 初始化操作
     */
    public function _initialize()
    {
        $this->conn = new Weixin();
    }

    /**
     * 获取所有wechat爬虫文章
     *
     * @return \think\Response
     */
    public function index()
    {
        $request = $this->getLimit();
        $title = $this->request->get('title');
        $keyword = $this->request->get('keyword_id');
        $where = [];
        if (!empty($title)) {
            $where["title"] = ["like", "%$title%"];
        }
        if (!empty($keyword)) {
            $where["keyword_id"] = $keyword;
        }
        $data = $this->conn->getArticle($request["limit"], $request["rows"], $where);
        return $this->resultArray('', '', $data);
    }


    /**
     * 获取某个文章
     * @param $id
     * @return array
     */
    public function read($id)
    {
        return $this->resultArray('', '', $this->conn->getOne($id));
    }

}
