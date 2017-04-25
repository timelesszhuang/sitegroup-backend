<?php

namespace app\common\controller;

use think\Controller;
use think\Request;
use think\Validate;

class Company extends Common
{
    /**
     * 显示资源列表
     * @auther guozhen
     * @return \think\Response
     */
    public function index()
    {
        $request = $this->getLimit();
        $name = $this->request->get('industry_name');
        $id = $this->request->get('industry_id');
        $where = [];
        if (!empty($name)) {
            $where["name"] = ["like", "%$name%"];
        }
        if (!empty($id)) {
            $where["industry_id"] = $id;
        }
        return $this->resultArray('', '', (new \app\common\model\Company())->getCompany($request["limit"], $request["rows"], $where));
    }

    /**
     * 显示创建资源表单页.
     * @auther guozhen
     * @return \think\Response
     */
    public function create()
    {

    }

    /**
     * 保存新建的资源
     * @auther guozhen
     * @param  \think\Request $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $rule = [
            ["name", "require|unique:Company", "请输入公司名称|公司名重复"],
            ["artificialperson", "require", "请输入法人"],
            ["manbusiness", "require", "请输入主营业务"],
            ["industry_id", "require", "请选择行业"],
            ["industry_name", "require", "请选择行业"]
        ];
        $validate = new Validate($rule);
        $data = $this->request->post();
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), "failed");
        }
        if (!\app\common\model\Company::create($data)) {
            return $this->resultArray("添加失败", "failed");
        }
        return $this->resultArray("添加成功");
    }

    /**
     * 显示指定的资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function read($id)
    {
        return $this->resultArray('', '', \app\common\model\Company::get($id));
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request $request
     * @param  int $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        $rule = [
            ["name", "require", "请输入公司名称"],
            ["artificialperson", "require", "请输入法人"],
            ["manbusiness", "require", "请输入主营业务"],
            ["industry_id", "require", "请选择行业"],
            ["industry_name", "require", "请选择行业"]
        ];
        $data = $this->request->put();
        $validate = new Validate($rule);
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), 'failed');
        }
        if (!\app\common\model\Company::update($data)) {
            return $this->resultArray('修改失败', 'failed');
        }
        return $this->resultArray('修改成功');
    }

    /**
     * 删除指定资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $Industry = \app\common\model\Company::get($id);
        if (!$Industry->delete()) {
            return $this->resultArray('删除失败', 'failed');
        }
        return $this->resultArray('删除成功');
    }

    /**
     * 获取所有的公司信息 包括id和name
     * @return array
     */
    public function getAll()
    {
        $data = (new \app\common\model\Company)->field("id,name")->select();
        return $this->resultArray('', '', $data);
    }
}
