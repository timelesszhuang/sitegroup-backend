<?php

namespace app\admin\controller;

use app\common\controller\Common;
use think\Request;
use think\Validate;

/**
 * @author xingzhuang
 * 2017年5月16
 */
class Activity extends Common
{

    //该目录是相对于 public  使用 ROOT_PATH 需 手动追加 public/ 目录
    static $activitypath = 'upload/activity/zipactivity';


    /**
     * 显示资源列表
     * @return \think\Response
     * @author xingzhuang
     */
    public function index()
    {
        $request = $this->getLimit();
        $name = $this->request->get('name');
        $where = [];
        if (!empty($name)) {
            $where["title"] = ["like", "%$name%"];
        }
        $user = (new Common())->getSessionUser();
        $where["node_id"] = $user["user_node_id"];
        $data = (new \app\admin\model\Activity())->getActivity($request["limit"], $request["rows"], $where);
        return $this->resultArray('', '', $data);
    }

    /**
     * 显示创建资源表单页.
     * @return \think\Response
     * @author xingzhuang
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

    }

    /**
     * 显示指定的资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function read($id)
    {
        return $this->getread((new \app\admin\model\Activity), $id);
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
            ['name', "require", "请填写模板名"],
            ['detail', 'require', "请填写模板信息"],
        ];
        $validate = new Validate($rule);
        $data = $this->request->put();
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), 'failed');
        }
        return $this->publicUpdate((new \app\admin\model\Activity()), $data, $id);
    }

    /**
     * 删除指定资源 模板暂时不支持删除操作
     * @param  int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $Activity = new \app\admin\model\Activity();
        $user = $this->getSessionUser();
        $where["parent_id"] = $id;
        $where["node_id"] = $user["user_node_id"];
        if ($Activity->where(["id" => $id, "node_id" => $user["user_node_id"]])->delete()) {
            return $this->resultArray('删除成功', 'failed');
        }
        return $this->resultArray('删除成功');
    }

    /**
     * 上传关键词文件文件
     * @return array
     */
    public function uploadActivity()
    {
        $file = request()->file('file_name');
        $info = $file->move(ROOT_PATH . 'public/' . self::$activitypath);
        //要解压到的位置
        $dest = 'upload/activity/activity/';
//      $path = 'upload/activity/zipactivity/demo.zip';
        $file_savename = $info->getSaveName();
        $demo_path = $dest . pathinfo($file_savename);
        $this->unzipFile($file_savename, $dest);
        if ($info) {
            return $this->resultArray('上传成功', '', ['code_path' => $file_savename, 'demo_path' => $demo_path]);
        } else {
            // 上传失败获取错误信息
            return $this->resultArray('上传失败', 'failed', $info->getError());
        }
    }


    /**
     * 根据上传的文件名 导入关键词
     * @param Request $request
     * @return array
     * @author guozhen
     */
    public function addActivity(Request $request)
    {
        $post = $request->post();
        $rule = [
            ["name", "require", "请传入模板名"],
            ["detail", "require", "请传入模板详情"],
            ["path", "require", "请传入path"]
        ];
        $validate = new Validate($rule);
        if (!$validate->check($post)) {
            return $this->resultArray($validate->getError(), 'failed');
        }
        $post['path'] = self::$Activitypath . $post['path'];
        $user = (new Common())->getSessionUser();
        $post["node_id"] = $user["user_node_id"];
        $model = new \app\admin\model\Activity();
        $model->save($post);
        if ($model->id) {
            return $this->resultArray("添加成功");
        }
        return $this->resultArray('添加失败', 'failed');
    }

}