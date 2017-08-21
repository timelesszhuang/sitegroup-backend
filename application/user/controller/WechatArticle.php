<?php

namespace app\user\controller;

use think\Controller;
use think\Request;
use app\common\controller\Common;
use app\admin\model\WeixinArticle as Weixin;
use think\Validate;

class WechatArticle extends Common
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
        $keyword= $this->request->get('keyword_id');
        $where = [];
        if (!empty($title)) {
            $where["title"] = ["like", "%$title%"];
        }
        if(!empty($keyword)){
            $where["keyword_id"]=$keyword;
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
        return $this->resultArray('','',$this->conn->getOne($id));
    }

    /**
     * 添加wechat文章
     * @param Request $request
     * @return array
     */
    public function create(Request $request)
    {
        $rule = [
            ["title", "require", "请输入标题"],
            ["content", "require", "请输入内容"],
            ["articletype_id", "require", "请选择文章分类"],
        ];
        $validate = new Validate($rule);
        $data = $request->post();
//        dump($/home/qiangbi/桌面/template/index.htmldata);die;
        $user = $this->getSessionUser();
        $data['node_id'] = $user['user_node_id'];
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), "failed");
        }
        $data['summary'] = $this->utf8chstringsubstr($data['content'], 40 * 3);
        $data["is_collection"]=20;
        if (!\app\admin\model\Article::create($data)) {
            return $this->resultArray("添加失败", "failed");
        }
        return $this->resultArray("添加成功");
    }
}
