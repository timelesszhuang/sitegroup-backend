<?php

namespace app\admin\controller;

use app\common\controller\Common;
use app\sysadmin\model\Node;
use think\Request;
use think\Validate;
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
        $user = $this->getSessionUser();
        $where["parent_id"] = $id;
        $where["node_id"] = $user["user_node_id"];
        $key = $keyword->where($where)->select();
        if (!empty($key)) {
            return $this->resultArray('不能删除', 'failed');
        }
        if ($keyword->where(["id" => $id, "node_id" => $user["user_node_id"]])->delete() == false) {
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
            return $this->resultArray('上传失败', 'failed', $info->getError());
        }
    }

    /**
     * 根据上传的文件名 导入关键词
     * @param Request $request
     * @return array
     * @auther guozhen
     */
    public function insertKeyword(Request $request)
    {
        $post = $request->post();
        $rule = [
            ["id", "require", "请传入id"],
            ["path", "require", "请传入path"]
        ];
        $validate = new Validate($rule);
        if (!$validate->check($post)) {
            return $this->resultArray($validate->getError(), 'failed');
        }
        $model = new \app\admin\model\Keyword();
        $file_info=$this->getKeywordInfo($post["path"], $post["id"],$model);
        while ($key = fgets($file_info["file"])) {
            $getkey = $model->where(["name" => $key])->find();
            if (!empty($getkey)) {
                continue;
            }
            \app\admin\model\Keyword::create([
                "name" => $key,
                "parent_id" => $post["id"],
                "path" => $file_info["path"],
                "tag" => $file_info["tag"],
                "node_id" => $file_info["user_node_id"]
            ]);
        }
        return $this->resultArray("添加成功");
    }

    /**
     * 获取文件信息
     * @param $file_path
     * @param $id
     * @param $model
     * @return array
     * @auther guozhen
     */
    public function getKeywordInfo($file_path, $id,$model)
    {
        $file_path = "upload/" . $file_path;
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
            $user = $this->getSessionUser();
            return [
                "tag"=>$tag,
                "path"=>$path,
                "file"=>$file,
                "user_node_id"=>$user["user_node_id"]
            ];
        }
        exit(json_encode($this->resultArray('文件不存在', "failed")));
    }
}
