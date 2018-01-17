<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\common\controller\Common;
use app\admin\model\LibraryArticle as Library;
use think\Validate;

class LibraryArticle extends Common
{
    use Obtrait;
    use Osstrait;
    protected $conn='';
    /**
     * 初始化操作
     */
    public function _initialize()
    {
        $this->conn=new Library();
    }

    /**
     * 获取所有爬虫文章
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
        $user = $this->getSessionUser();
        $where["node_id"] = $user["user_node_id"];
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
     * 图片上传到 oss相关操作
     * @access public
     */
    public function imageupload()
    {
        $data = $this->uploadImg("libraryimgset/");
        if ($data['status']) {
            $data["msg"] = "上传成功";
            return $data;
        } else {
            return $this->resultArray('上传失败', 'failed');
        }
    }

}
