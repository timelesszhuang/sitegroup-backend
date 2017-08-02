<?php

namespace app\sysadmin\controller;

use app\common\controller\Common;
use think\Request;
use \app\admin\model\WeixinKeywordType as Type;
use think\Validate;

class KeywordType extends Common
{
    protected $conn = '';

    /**
     * 初始化操作
     */
    public function _initialize()
    {
        $this->conn = new Type();
    }

    /**
     * 获取所有关键词分类
     *
     * @return \think\Response
     */
    public function index()
    {
        $request = $this->getLimit();
        $name = $this->request->get('name');
        $where = [];
        if (!empty($name)) {
            $where["name"] = ["like", "%$name%"];
        }
        $data = $this->conn->getKeywordType($request["limit"], $request["rows"], $where);
        return $this->resultArray('', '', $data);
    }

    /**
     * 添加关键词分类
     */
    public function addKeywordType()
    {
        $rule = [
            ['name', 'require', "请填分类名"],
            ["detail", "require", "请填详情"],
        ];
        $validate = new Validate($rule);
        $data = $this->request->post();
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), "failed");
        }
        if (!Type::create($data)) {
            return $this->resultArray("添加失败", "failed");
        }
        return $this->resultArray("添加成功");
    }
    /**
     * 修改关键词分类
     */
    public function editKeywordType()
    {
        $rule = [
            ['name', 'require', "请填分类名"],
            ["detail", "require", "请填详情"],
        ];
        $validate = new Validate($rule);
        $data = $this->request->post();
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), "failed");
        }
        if (!Type::update($data)) {
            return $this->resultArray("修改失败", "failed");
        }
        return $this->resultArray("修改成功");
    }



}
