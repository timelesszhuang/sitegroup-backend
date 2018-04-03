<?php

namespace app\wx\controller;

use app\common\controller\Common;
use app\common\controller\CommonLogin;
use app\common\exception\ProcessException;
use app\common\model\Menu;
use app\common\model\Site;
use think\Model;
use think\Request;
use think\Validate;
use app\common\traits\Obtrait;
use app\common\traits\Osstrait;
use app\common\model\LibraryImgset;

class WxProduct extends Common
{
    use Obtrait;
    use Osstrait;

    public function __construct()
    {
        parent::__construct();
        $this->model = new \app\wx\model\WxProduct();
    }

    /**
     * 显示资源列表
     *
     * @param Request $request
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author jingzheng
     */
    public function index(Request $request)
    {
        $limits = $this->getLimit();
        $content = $request->get('title');
        $menu_id = $request->get('menu_id');
        $where = [];
        if (!empty($content)) {
            $where['title'] = ["like", "%$content%"];
        }
        if (!empty($menu_id)) {
            $where['menu_id'] = $menu_id;
        }
        $user = $this->getSessionUserInfo();
        $app_id = (new \app\wx\model\WxSmallApp())->getAppId($user["node_id"]);
        $where["app_id"] = $app_id;
        return $this->resultArray($this->model->getAll($limits['limit'], $limits['rows'], $where));
    }

    /**
     * 保存新建的资源
     *
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * @author jingzheng
     */
    public function save()
    {
        try {
            $rule = [
                ['name', "require", "请填写产品名称"],
                ['thumbnails', 'require', "请选择产品主图"],
                ["price", "require", "请填写产品价格"],
                ["info", "require", "请填写产品详情"],
            ];
            $validate = new Validate($rule);
            $data = $this->request->post();
            if (!$validate->check($data)) {
                Common::processException($validate->getError());
            }
            $data["app_id"] = $app_id = (new \app\wx\model\WxSmallApp())->getAppId($this->getSessionUserInfo()["node_id"]);
            if (!$this->model->save($data)) {
                Common::processException('添加失败');
            }
            return $this->resultArray('添加成功');
        } catch (ProcessException $e) {
            return $this->resultArray('failed', $e->getMessage());
        }
    }

    /**
     * 显示指定的资源
     *
     * @param  int $id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author jingzheng
     */
    public function read($id)
    {
        $data = $this->getread($this->model, $id);
        $data['data']['tags'] = implode(',', array_filter(explode(',', $data['data']['tags'])));
        $data['data']['flag'] = implode(',', array_filter(explode(',', $data['data']['flag'])));
        return $data;
    }

    /**
     * 保存更新的资源
     * @param  int $id
     * @return array|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * @author jingzheng
     */
    public function update($id)
    {
        try {
            $rule = [
                ['name', "require", "请填写产品名称"],
                ['thumbnails', 'require', "请选择产品主图"],
                ["price", "require", "请填写产品价格"],
                ["info", "require", "请填写产品详情"],
            ];
            $validate = new Validate($rule);
            $data = $this->request->post();
            if (!$validate->check($data)) {
                Common::processException($validate->getError());
            }
            if(!$data['app_id']){
                $data["app_id"] = $app_id = (new \app\wx\model\WxSmallApp())->getAppId($this->getSessionUserInfo()["node_id"]);
            }
            if (!$this->model->isUpdate(true)->save($data)) {
                Common::processException('添加失败');
            }
            return $this->resultArray('添加成功');
        } catch (ProcessException $e) {
            return $this->resultArray('failed', $e->getMessage());
        }
    }

    /**
     * 删除指定资源
     *
     * @param  int $id
     * @return array
     * @author jingzheng
     */
    public function delete($id)
    {
        return $this->deleteRecord($this->model, $id);
    }
}
