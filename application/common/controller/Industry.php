<?php
/**
 * Created by PhpStorm.
 * User: jingzheng
 * Date: 2017/4/21
 * Time: 11:35
 */

namespace app\common\controller;

use think\Validate;
use think\Request;
use app\common\model\Industry as this_model;

class Industry extends CommonLogin
{
    /**
     * 初始化操作
     */
    public function __construct()
    {
        parent::__construct();
        $this->model = new this_model();
    }

    /**
     * 查询数据
     * @param $id
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \think\exception\DbException
     * @author jingzheng
     */
    public function read($id)
    {
        return $this->resultArray($this->model->get($id)->toArray());

    }

    /**
     * 分页数据
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author jingzheng
     */
    public function index()
    {

        $request = $this->getLimit();
        $name = $this->request->get('name');
        $id = $this->request->get('id');
        $where = [];
        if (!empty($name)) {
            $where["name"] = ["like", "%$name%"];
        }
        if (!empty($id)) {
            $where["id"] = $id;
        }
        return $this->resultArray($this->model->getIndustry($request["limit"], $request["rows"], $where));

    }

    /**
     * 添加数据
     * @return array
     * @author jingzheng
     */
    public function save(Request $request)
    {

        $rule = [
            ["name", "require|unique:Industry", "请输入行业名|行业名重复"],
            ['detail', 'require', '详细必须'],
        ];
        $data = $this->request->post();
        $validate = new Validate($rule);
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), 'failed');
        }
        if (!$this->model->create($data)) {
            return $this->resultArray('添加失败', 'failed');
        }
        return $this->resultArray('添加成功');
    }


    /**
     * 修改数据
     * @return array
     * @author jingzheng
     */
    public function update(Request $request, $id)
    {
        $rule = [
            ["name", "require|unique:Industry", "请输入行业名|行业名重复"],
            ['detail', 'require', '详细必须'],
        ];
        $data = $this->request->put();
        $validate = new Validate($rule);
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), 'failed');
        }
        if (!$this->model->update($data)) {
            return $this->resultArray('修改失败', 'failed');
        }
        return $this->resultArray('修改成功');

    }


    /**
     * 删除
     * @param  int $id
     * @return array
     * @throws \think\exception\DbException
     * @author jingzheng
     */
    public function delete($id)
    {
        $Industry = $this->model->get($id);
        if (!$Industry->delete()) {
            return $this->resultArray('删除失败', 'failed');
        }
        return $this->resultArray('删除成功');
    }

    /**
     * 获取行业id name
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author jingzheng
     */

    public function getIndustry()
    {
        return $this->resultArray($this->model->getSort());
    }
}

