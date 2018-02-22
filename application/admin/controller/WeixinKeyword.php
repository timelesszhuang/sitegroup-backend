<?php

namespace appcp\admin\controller;

use app\common\controller\Common;
use think\Request;
use \app\admin\model\WeixinKeyword as Scrapy;
use think\Validate;

class WeixinKeyword extends Common
{
    protected $conn = '';

    /**
     * 初始化操作
     */
    //TODO oldfunction
    public function _initialize()
    {
        $this->conn = new Scrapy();
    }

    /**
     * 获取关键字
     *
     * @return \think\Response
     */
    //TODO oldfunction
    public function index()
    {
        $request = $this->getLimit();
        $name = $this->request->get('name');
        $id = $this->request->get('keyword_typeid');
        $where = [];
        if (!empty($name)) {
            $where["name"] = ["like", "%$name%"];
        }
        if (!empty($id)) {
            $where["type_id"] = $id;
        }
        $data = $this->conn->getKeyword($request["limit"], $request["rows"], $where);
        return $this->resultArray('', '', $data);
    }

    /**
     * 添加操作
     *
     * @return \think\Response
     */
//TODO oldfunction
    public function create()
    {
        $rule = [
            ['name', 'require', "请填写关键词名字"],
            ["detail", "require", "请输入关键词描述"],
            ['type_name', 'require', '请填写分类名']
        ];
        $validate = new Validate($rule);
        $data = $this->request->post();

        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), "failed");
        }
        if (!Scrapy::create($data)) {
            return $this->resultArray("添加失败", "failed");
        }
        return $this->resultArray("添加成功");
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request $request
     * @return \think\Response
     */
//TODO oldfunction
    public function save()
    {
        $rule = [
            ['name', 'require', "请填写关键词名字"],
            ["detail", "require", "请输入关键词描述"],
            ['type_name', 'require', '请填写分类名']
        ];
        $validate = new Validate($rule);
        $data = $this->request->post();
        $data['create_time'] = strtotime($data['create_time']);
        $data['update_time'] = strtotime($data['update_time']);
        $data['status'] = 20;
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), "failed");
        }
        if (!Scrapy::update($data)) {
            return $this->resultArray("修改失败", "failed");
        }
        return $this->resultArray("修改成功");
    }

    /**
     * 获取一条数据
     *
     * @param  int $id
     * @return \think\Response
     */
//TODO oldfunction
    public function read($id)
    {
        return $this->resultArray('', '', $this->conn->getOne($id));
    }

    /**
     * 停止爬取
     * @param $id
     * @return array
     */
//TODO oldfunction
    public function stopScrapy($id)
    {
        return $this->resultArray('', '', $this->conn->stopScrapy($id));
    }

    /**
     * 获取列表
     * @return array
     */
//TODO oldfunction
    public function getKeyList()
    {
        return $this->resultArray('', '', $this->conn->getKeyList());
    }


}
