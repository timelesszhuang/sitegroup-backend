<?php

namespace app\sysadmin\controller;

use app\common\controller\Common;
use think\Request;
use think\Validate;
use app\admin\model\Template as tem;
use app\common\traits\Obtrait;
use app\common\traits\Osstrait;

class Template extends Common
{
    use Osstrait;
    use Obtrait;

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $request = $this->getLimit();
        $name = $this->request->get('name');
        $where = [];
        if (!empty($name)) {
            $where["name"] = ["like", "%$name%"];
        }
        $where["node_id"] = ["lt", 1];
        $data = (new \app\admin\model\Template())->getTemplate($request["limit"], $request["rows"], $where);
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
        $rule = [
            ["name", "require", "请先填写模板名"],
            ["thumbnails", "require", "请先上传缩略图"],
            ["show_path", "require", "请先上传未替换的模板"],
            ["industry_name", "require", "请上传行业名称"],
            ["industry_id", "require", "请上传行业id"]
        ];
        $validate = new Validate($rule);
        $post = $request->post();
        if (!$validate->check($post)) {
            return $this->resultArray($validate->getError(), "failed");
        }
        $tem = new tem();
        if (!$tem->allowField(true)->save($post)) {
            return $this->resultArray("添加失败", "failed");
        }
        return $this->resultArray("添加成功!!");
    }

    /**
     * 显示指定的资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function read($id)
    {
        return $this->getread(new tem(), $id);
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
            ["name", "require", "请先填写模板名"],
            ["thumbnails", "require", "请先上传缩略图"],
            ["show_path", "require", "请先上传未替换的模板"],
            ["industry_name", "require", "请上传行业名称"],
            ["industry_id", "require", "请上传行业id"]
        ];
        $validate = new Validate($rule);
        $put = $request->put();
        if (!$validate->check($put)) {
            return $this->resultArray($validate->getError(), "failed");
        }
        if (!tem::update($put, ["id" => $id])) {
            return $this->resultArray("修改失败!", "failed");
        }
        return $this->resultArray("修改成功!!");
    }

    /**
     * 删除指定资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }

    /**
     * 上传嵌套后的模板文件
     * @return array
     */
    public function uploadPHPTemplate()
    {
        $data = $this->uploadImg("template/");
        if ($data['status']) {
            return $this->resultArray('上传成功', $data['status'], $data['url']);
        } else {
            return $this->resultArray('上传失败', 'failed');
        }
    }

    /**
     * 上传原始模板
     * @return array
     * @param $src 原始zip目录
     * @param $obj 解压缩后的目录
     * @param $uploadpath 解压的目录
     */
    public function uploadTemplate()
    {
        $request = Request::instance();
        $template = $request->file("file");
        $path = "/upload/srctemplate/";
        $info = $template->move(ROOT_PATH . "public" . $path);
        if ($info) {
            $uploadpath = "/upload/zipsrctemplate/";
            $src = ROOT_PATH . "public" . $path . $info->getSaveName();
            $obj = ROOT_PATH . 'public' . $uploadpath;
            if($this->checkZipDirectory($src,$uploadpath)){
                return $this->resultArray("同名称模板已经存在,请修改","failed");
            }
            $url = $this->ZipArchive($src, $obj, $uploadpath);
            $data = $this->uploadTemp("template/" . $info->getSaveName(), $src);
            if ($data['status']) {
                 $dataurl = [
                   'url' =>$data['url'],
                   'data' =>$url
                 ];
                return $this->resultArray('上传成功', '', $dataurl);
            } else {
                return $this->resultArray('上传失败', 'failed');
            }

        }
        return $this->resultArray("上传失败", "failed");

    }

    /**
     * 上传缩略图
     * @return array
     */
    public function uploadThumbnails()
    {
        $data = $this->uploadImg("template/");
        if ($data['status']) {
            return $this->resultArray('上传成功', $data['status'], $data['url']);
        } else {
            return $this->resultArray('上传失败', 'failed');
        }
    }
}
