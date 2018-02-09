<?php

namespace app\common\controller;

use app\common\model\LibraryArticle as Library;

class LibraryArticle extends CommonLogin
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new Library();
    }

    /**
     * 获取所有爬虫文章
     *
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $request = $this->getLimit();
        $title= $this->request->get('title');
        $where = [];
        if (!empty($title)) {
            $where["title"] = ["like", "%$title%"];
        }
        $user = $this->getSessionUserInfo();
        $where["node_id"] = $user["node_id"];
        $data = $this->model->getArticle($request["limit"], $request["rows"], $where);
        return $this->resultArray($data);
    }

    /**
     * 获取某个文章
     * @param $id
     * @return array
     * @throws \think\exception\DbException
     */
    public function read($id)
    {
        return $this->resultArray($this->model->getOne($id)->toArray());
    }
}
