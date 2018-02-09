<?php

namespace app\common\controller;

use app\common\model\Sohunews;

class Souhu extends CommonLogin
{

    public function __construct()
    {
        parent::__construct();
        $this->model = new Sohunews();
    }

    /**
     * 显示资源列表
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $request = $this->getLimit();
        $title = $this->request->get('title');
        $type_id = $this->request->get('type_id');
        $where = [];
        if (!empty($title)) {
            $where["title"] = ["like", "%$title%"];
        }
        if (!empty($type_id)) {
            $where["type_id"] = $type_id;
        }
        $data = $this->model->getArticle($request["limit"], $request["rows"], $where);
        return $this->resultArray($data);
    }

    /**
     * 显示指定的资源
     * @param  int $id
     * @return array
     * @throws \think\exception\DbException
     */
    public function read($id)
    {
        return $this->resultArray($this->model->getOne($id)->toArray());
    }

    /**
     * 通过id获取分类
     * @return mixed|string
     */
    public function getTypes()
    {
        $arr = [
            ["id" => 1, "text" => "科技类"],
            ["id" => 2, "text" => "教育类"],
            ["id" => 3, "text" => "财经类"],
            ["id" => 4, "text" => "美食类"],
            ["id" => 5, "text" => "社会类"],
            ["id" => 6, "text" => "文化类"]
        ];
        return $this->resultArray($arr);
    }
}