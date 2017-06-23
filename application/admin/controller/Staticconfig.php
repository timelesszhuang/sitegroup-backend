<?php

namespace app\admin\controller;

use app\common\controller\Common;
use think\Session;
use think\Validate;
use think\Request;

class Staticconfig extends Common
{
    /**
     * @return array
     */
    public function index()
    {
        $request = $this->getLimit();
        $site_id = $this->request->get('site_id');
        $where = [];
        if (!empty($site_id)) {
            $where["site_id"] = ["like", "%$site_id%"];
        }
        $user = $this->getSessionUser();
        $where["node_id"] = $user["user_node_id"];
        $data = (new \app\admin\model\SiteStaticconfig())->getAll($request["limit"], $request["rows"], $where);
        return $this->resultArray('', '', $data);
    }

    /**
     * @param $id
     * @return array
     */
    public function read($id)
    {
        return $this->getread((new \app\admin\model\SiteStaticconfig), $id);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $rule = [
            ["starttime", "require", "请输入开始时间"],
            ["stoptime", "require", "请输入结束时间"],
            ["staticcount", "require", "请输入生成的文章数量"],
            ["site_id", "require", "请选择站点"],
        ];
        $validate = new Validate($rule);
        $data = $request->post();
        $user = $this->getSessionUser();
        $data['node_id'] = $user['user_node_id'];
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), "failed");
        }
        $where = [];
        $where['site_id'] = $data['site_id'];
        $Staticdata = (new \app\admin\model\SiteStaticconfig())->where($where)->select();
        foreach ($Staticdata as $k => $v) {
            if (!(strtotime($data['stoptime']) < strtotime($v['starttime']) || strtotime($data['starttime']) > strtotime($v['stoptime']))) {
                return $this->resultArray("当前时间段已有相关配置,请查证后再试", "failed");
            }
        }
        if (!\app\admin\model\SiteStaticconfig::create($data)) {
            return $this->resultArray("添加失败", "failed");
        }
        return $this->resultArray("添加成功");
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
            ["starttime", "require", "请输入开始时间"],
            ["stoptime", "require", "请输入结束时间"],
            ["staticcount", "require", "请输入生成的文章数量"],
            ["site_id", "require", "请选择站点"],
        ];
        $data = $request->put();
        $validate = new Validate($rule);
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), 'failed');
        }
        $where = [];
        $where['site_id'] = $data['site_id'];
        $Staticdata = (new \app\admin\model\SiteStaticconfig())->where($where)->select();
        foreach ($Staticdata as $k => $v) {
            if (!(strtotime($data['stoptime']) < strtotime($v['starttime']) || strtotime($data['starttime']) > strtotime($v['stoptime']))) {
                return $this->resultArray("当前时间段已有相关配置,请查证后再试", "failed");
            }
        }
        if (!(new \app\admin\model\SiteStaticconfig)->save($data, ["id" => $id])) {
            return $this->resultArray('修改失败', 'failed');
        }
        return $this->resultArray('修改成功');
    }

    /**
     * 删除指定资源
     * @param  int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        return $this->deleteRecord((new \app\admin\model\SiteStaticconfig), $id);
    }


}
