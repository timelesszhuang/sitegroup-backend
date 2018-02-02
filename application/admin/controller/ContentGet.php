<?php

namespace app\admin\controller;

use app\common\controller\Common;
use app\common\controller\CommonLogin;
use app\common\exception\ProcessException;
use app\common\traits\Osstrait;
use think\Validate;
use think\Request;
use app\common\traits\Obtrait;

class ContentGet extends CommonLogin
{
    use Obtrait;
    use Osstrait;

    public function __construct()
    {
        parent::__construct();
        $this->model = new \app\common\model\ContentGet();
    }

    /**
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $request = $this->getLimit();
        $name = $this->request->get('name');
        if ($name) {
            $where["name"] = ["like", "%$name%"];
        }
        $user = $this->getSessionUserInfo();
        $where["node_id"] = $user["node_id"];
        $data = $this->model->getContentList($request["limit"], $request["rows"], $where);
        return $this->resultArray('', '', $data);
    }

    /**
     * @param $id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function read($id)
    {
        return $this->getread($this->model, $id);
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request $request
     * @return array
     */
    public function save(Request $request)
    {
        try {
            $rule = [
                ["name", "require", "请输入名称"],
                ["en_name", "require|alphaNum|unique:imglist,en_name^node_id", "请输入英文名称|英文名格式只支持字母与数字|英文名重复"],
                ["href", "url", "链接格式不正确"],
            ];
            $validate = new Validate($rule);
            $data = $request->post();
            $user = $this->getSessionUserInfo();
            $data['node_id'] = $user['node_id'];
            if (!$validate->check($data)) {
                Common::processException($validate->getError());

            }
            if (!$this->model->create($data)) {
                Common::processException('添加失败');
            }
            return $this->resultArray("添加成功");
        } catch (ProcessException $e) {
            return $this->resultArray('failed', $e->getMessage());
        }
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request $request
     * @param  int $id
     * @return array
     */
    public function update(Request $request, $id)
    {
        try {
            $rule = [
                ["name", "require", "请输入名称"],
                ["en_name", "require|alphaNum|unique:imglist,en_name^node_id", "请输入英文名称|英文名格式只支持字母与数字|英文名重复"],
                ["href", "url", "链接格式不正确"],
            ];
            $data = $request->put();
            $validate = new Validate($rule);
            if (!$validate->check($data)) {
                Common::processException($validate->getError());
            }
            if (!$this->model->save($data, ["id" => $id])) {
                Common::processException('修改失败');
            }
            return $this->resultArray("修改成功");
        } catch (ProcessException $e) {
            return $this->resultArray('failed', $e->getMessage());
        }
    }

    /**
     * 删除文章
     * @param $id
     * @return array
     */
    public function delete($id)
    {
        return $this->deleteRecord($this->model, $id);
    }

}
