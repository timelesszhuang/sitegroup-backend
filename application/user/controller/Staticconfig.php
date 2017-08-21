<?php

namespace app\user\controller;

use app\admin\model\SiteType;
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
        $node_id = $this->getSiteSession('login_site');
        $where = [];
        $where["node_id"] = $node_id["node_id"];
        $where["site_id"] = $this->getSiteSession('website')["id"];
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
            ["type", "require", "请输入类型"],
        ];
        $validate = new Validate($rule);
        $data = $request->post();
        $node_id = $this->getSiteSession('login_site');
        $data['node_id'] = $node_id["node_id"];
        $data['site_id'] = $this->getSiteSession('website')["id"];
        $data['site_name'] = $this->getSiteSession('website')['site_name'];
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), "failed");
        }
        $where = [];
        $where["node_id"] = $data['node_id'];
        $where["site_id"] = $data['site_id'];
        $where['type'] = $data['type'];
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
            ["type", "require", "请输入类型"],
        ];
        $data = $request->put();
        $validate = new Validate($rule);
        $node_id = $this->getSiteSession('login_site');
        $data['node_id'] = $node_id["node_id"];
        $data['site_id'] = $this->getSiteSession('website')["id"];
        $data['site_name'] = $this->getSiteSession('website')['site_name'];
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), "failed");
        }
        $where = [];
        $where["node_id"] = $data['node_id'];
        $where["site_id"] = $data['site_id'];
        $where['type'] = $data['type'];
        $Staticdata = (new \app\admin\model\SiteStaticconfig())->where($where)->select();
        foreach ($Staticdata as $k => $v) {
            if (!(strtotime($data['stoptime']) < strtotime($v['starttime']) || strtotime($data['starttime']) > strtotime($v['stoptime']))) {
                if($data['id']==$v['id']){
                    continue;
                }
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
