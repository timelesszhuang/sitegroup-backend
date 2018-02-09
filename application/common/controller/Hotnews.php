<?php

namespace app\common\controller;

use app\common\model\Hotnews as hot;

class Hotnews extends CommonLogin
{

    /**
     * 初始化操作
     */
    public function __construct()
    {
        parent::__construct();
        $this->model = new hot();
    }

    /**
     * 显示资源列表
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
        $data = $this->model->getHot($request["limit"], $request["rows"], $where);
        return $this->resultArray('', '', $data);
    }

    /**
     * 获取某个文章
     * @param $id
     * @return array
     * @throws \think\exception\DbException
     */
    public function read($id)
    {
        return $this->resultArray('','',$this->model->getOne($id)->toArray());
    }
}
