<?php

namespace app\admin\controller;

use app\common\controller\Common;
use think\Request;
use think\Validate;

class Template extends Common
{

    static $templatepath = 'public/upload/template';

    /**
     * 显示资源列表
     * @return \think\Response
     * @author jingzheng
     */
    public function index()
    {
        $tag = "";
        $id = $this->request->get('id');
        if (empty($id)) {
            $tag = "A";
        }
        $data = (new \app\admin\model\Keyword())->getKeyword($tag, $id);
        return $this->resultArray('', '', $data);
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

    }

    /**
     * 显示指定的资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function read($id)
    {
        return $this->getread((new \app\admin\model\Keyword), $id);
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
        //
    }

    /**
     * 删除指定资源
     * @param  int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $keyword = new \app\admin\model\Keyword();
        $user = $this->getSessionUser();
        $where["parent_id"] = $id;
        $where["node_id"] = $user["user_node_id"];
        $key = $keyword->where($where)->select();
        if (!empty($key)) {
            return $this->resultArray('父级不能直接删除', 'failed');
        }
        if ($keyword->where(["id" => $id, "node_id" => $user["user_node_id"]])->delete() == false) {
            return $this->resultArray('父级节点不能删除', 'failed');
        }
        return $this->resultArray('删除成功');
    }

    /**
     * 上传关键词文件文件
     * @return array
     */
    public function uploadTemplate()
    {
        $file = request()->file('file_name');
        $info = $file->move(ROOT_PATH . self::$templatepath);
        if ($info) {
            return $this->resultArray('上传成功', '', $info->getSaveName());
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
    public function addTemplate(Request $request)
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
        $post['path'] = self::$templatepath . $post['path'];
        $model = new \app\admin\model\Template();
        $model->save($post);
        if ($model->id) {
            return $this->resultArray("添加成功");
        }
        return $this->resultArray('添加失败', 'failed');
    }


    /**
     * 添加A类关键词
     * @author guozhen
     * @return array
     */
    public function insertA()
    {
        $rule = [
            ["name", "require", "请填写A类关键词"],
        ];
        $validate = new Validate($rule);
        $data = $this->request->post();
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), 'faile');
        }
        $user = $this->getSessionUser();
        $data["node_id"] = $user["user_node_id"];
        if (!\app\admin\model\Keyword::create($data)) {
            return $this->resultArray('添加失败', "faile");
        }
        return $this->resultArray('添加成功');
    }
}