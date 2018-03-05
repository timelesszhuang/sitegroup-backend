<?php

namespace app\common\controller;

use think\Session;
use think\Validate;
use think\Request;

class Staticconfig extends CommonLogin
{
    /**
     *@return array
     */
    public function index()
    {
        $request = $this->getLimit();
        $site_id = $this->request->get('site_id');
        $where = [];
        if (!empty($site_id)) {
            $where["site_id"] = ["like", "%$site_id%"];
        }
        $user_info = $this->getSessionUserInfo();
        $where["node_id"] = $user_info["node_id"];
        if ($user_info['user_type_name'] == 'site' && $user_info['user_type'] == '3') {
            $where["site_id"] = $user_info["site_id"];
        }
        $data = (new \app\common\model\SiteStaticconfig())->getAll($request["limit"], $request["rows"], $where);
        return $this->resultArray('', '', $data);
    }

    /**
     * @param $id
     * @return array
     */
    public function read($id)
    {
        return $this->getread((new \app\common\model\SiteStaticconfig), $id);
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
        ];
        $validate = new Validate($rule);
        $data = $request->post();
        $user_info = $this->getSessionUserInfo();
        $data["node_id"] = $user_info["node_id"];
        if (!$validate->check($data)) {
            return $this->resultArray( "failed",$validate->getError());
        }
        $where = [];
        $where['site_id'] = $data['site_id'];
        $where['type']=$data['type'];
        $Staticdata = (new \app\common\model\SiteStaticconfig())->where($where)->select();
        foreach ($Staticdata as $k => $v) {
            if (!(strtotime($data['stoptime']) < strtotime($v['starttime']) || strtotime($data['starttime']) > strtotime($v['stoptime']))) {
                return $this->resultArray( "failed","当前时间段已有相关配置,请查证后再试");
            }
        }
        $user_info = $this->getSessionUserInfo();
        if ($user_info['user_type_name'] == 'site' && $user_info['user_type'] == '3') {
            $data["site_id"] = $user_info["site_id"];
        }
        if (!\app\common\model\SiteStaticconfig::create($data)) {
            return $this->resultArray( "failed","添加失败");
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
        ];
        $data = $request->put();
        $validate = new Validate($rule);
        if (!$validate->check($data)) {
            return $this->resultArray( 'failed',$validate->getError());
        }
        $where = [];
        $where['site_id'] = $data['site_id'];
        $where['type']=$data['type'];
        $Staticdata = (new \app\common\model\SiteStaticconfig())->where($where)->select();
        foreach ($Staticdata as $k => $v) {
            if($v['id']==$data['id']){
                continue;
            }
            if (!(strtotime($data['stoptime']) < strtotime($v['starttime']) || strtotime($data['starttime']) > strtotime($v['stoptime']))) {
                return $this->resultArray( "failed","当前时间段已有相关配置,请查证后再试");
            }
        }
        $user_info = $this->getSessionUserInfo();
        if ($user_info['user_type_name'] == 'site' && $user_info['user_type'] == '3') {
            $data["site_id"] = $user_info["site_id"];
        }
        if (!(new \app\common\model\SiteStaticconfig)->save($data, ["id" => $id])) {
            return $this->resultArray( 'failed','修改失败');
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
        return $this->deleteRecord((new \app\common\model\SiteStaticconfig), $id);
    }


}
