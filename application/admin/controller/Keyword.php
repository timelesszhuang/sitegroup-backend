<?php

namespace app\admin\controller;

use app\common\controller\Common;
use app\sysadmin\model\Node;
use think\Request;
use think\worker\Server;

class Keyword extends Common
{
    /**
     * 显示资源列表
     * @return \think\Response
     * @auther jingzheng
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
        return $this->resultArray('', '', \app\admin\model\Keyword::get($id));
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
        $user=$this->getSessionUser();
        $where["parent_id"]=$id;
        $where["node_id"]=$user["user_node_id"];
        $key = $keyword->where($where)->select();
        if (!empty($key)) {
            return $this->resultArray('不能删除', 'failed');
        }
        if ($keyword->where(["id" => $id])->delete()==false) {
            return $this->resultArray('删除失败', 'failed');
        }
        return $this->resultArray('删除成功');
    }

    /**
     * 上传关键词文件文件
     * @return array
     */
    public function uploadKeyword()
    {
        $file = request()->file('file_name');
        $info = $file->move(ROOT_PATH . 'public/upload');
        if ($info) {
            // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
            return $this->resultArray('上传成功', '', $info->getSaveName());
        } else {
            // 上传失败获取错误信息
            return $this->resultArray('上传成功', '', $info->getError());
        }
    }

    public function insertKeyword(Request $request)
    {
        $file_path = $request->post('path');
        $file_path = "upload/" . $file_path;
        $id = $request->post('id');
        $model = new \app\admin\model\Keyword();
        $user = $this->getSessionUser();
        if (file_exists($file_path)) {
            $oldKey = $model->where(["id" => $id])->find();
            $tag = "B";
            if ($oldKey["tag"] == "B") {
                $tag = "C";
            }
            $path = "," . $id . ",";
            if (!empty($oldKey["parent_id"])) {
                $path = "," . $oldKey["parent_id"] . "," . $id . ",";
            }
            $file = fopen($file_path, "r");
            while ($key = fgets($file)) {
                $getkey = $model->where(["name" => $key])->find();
                if (!empty($getkey)) {
                    continue;
                }
                \app\admin\model\Keyword::create([
                    "name" => $key,
                    "parent_id" => $id,
                    "path" => $path,
                    "tag" => $tag,
                    "node_id" => $user["user_node_id"]
                ]);
            }
            return $this->resultArray("添加成功");
        }
        return $this->resultArray("添加失败");
    }

}
