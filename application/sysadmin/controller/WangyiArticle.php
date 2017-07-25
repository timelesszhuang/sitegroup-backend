<?php

namespace app\sysadmin\controller;

use think\Controller;
use think\Request;
use app\common\controller\Common;
use \app\admin\model\WangyiArticle as Weixin;
use think\Validate;
class WangyiArticle extends Common
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
     * 获取某一篇文章
     * @param $id
     * @return array
     */
    public function getOne($id)
    {
        return $this->resultArray('','',$this->conn->getOne($id));
    }

    /**
     * 修改文章
     * @param Request $request
     * @return array
     */
    public function edit(Request $request)
    {
        $rule=[
            ["id","require","请选择文章"],
            ["title","require","请填写标题"],
            ["content","require","请填写内容"]
        ];
        $validate=new Validate($rule);
        $data=$request->post();
        if(!$validate->check($data)){
            return $this->resultArray($validate->getError(),"failed");
        }
        if($this->conn->editKeyword($data["id"],$data["title"],$data["content"])){
            return $this->resultArray('修改成功');
        }
        return $this->resultArray('修改失败', 'failed');
    }

    /**
     * 删除文章
     * @param $id
     * @return array
     */
    public function delete($id)
    {
        if($this->conn->deleteOne($id)){
            return $this->resultArray('删除成功');
        }
        return $this->resultArray('删除失败', 'failed');
    }

    /**
     * 获取所有分类
     * @return array
     */
    public function getTypes()
    {
        return $this->resultArray('','',$this->conn->allTypes());
    }

}
