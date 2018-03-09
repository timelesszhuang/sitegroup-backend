<?php

namespace app\common\controller;


use think\Request;
use think\Validate;
use app\common\traits\Obtrait;
use app\common\traits\Osstrait;
use app\common\exception\ProcessException;

class Template extends Common
{

    use Osstrait;
    use Obtrait;
    static $templatepath = 'upload/template/';

    /**
     * 显示资源列表
     * @return \think\Response
     * @author jingzheng
     */
    public function index()
    {
        $request = $this->getLimit();
        $name = $this->request->get('name');
        $where = [];
        if (!empty($name)) {
            $where["name"] = ["like", "%$name%"];
        }
        $user_info = $this->getSessionUserInfo();
        if ($user_info['user_type_name'] == 'node' && $user_info['user_type'] == 2) {
            $where["node_id"] = [["=", $user_info["node_id"]], ["=", 0], "or"];
        } else {
            $where["node_id"] = ["lt", 1];
        }
        $data = (new \app\common\model\Template())->getTemplate($request["limit"], $request["rows"], $where);
        return $this->resultArray('', '', $data);
    }


    /**
     * 显示指定的资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function read($id)
    {
        return $this->getread((new \app\common\model\Template), $id);
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
        try {
            $rule = [
                ['name', "require", "请填写模板名"],
            ];
            $validate = new Validate($rule);
            $data = $this->request->put();
            if (!$validate->check($data)) {
                Common::processException($validate->getError());
            }
            $model = new \app\common\model\Template();
            if (!$model->save($data, ["id" => $id])) {
                Common::processException('修改失败');
            }
            return $this->resultArray('修改成功');
        } catch (ProcessException $e) {
            return $this->resultArray('failed', $e->getMessage());
        }
    }


    /**
     * 上传模板
     * @return array
     */
    public function uploadTemplate()
    {
        try {
            $url = $this->uploadImg('template/');
            return $this->resultArray(['url' => $url], '上传成功');
        } catch (ProcessException $exception) {
            return $this->resultArray('failed', '上传失败');
        }
    }

    /**
     * 根据上传的文件名 导入关键词
     * @param Request $request
     * @return array
     * @author jingzheng
     */
    public function save(Request $request)
    {
        $post = $request->post();
        $rule = [
            ["name", "require", "请传入模板名"],
        ];
        $validate = new Validate($rule);
        if (!$validate->check($post)) {
            return $this->resultArray($validate->getError(), 'failed');
        }
        $user_info = $this->getSessionUserInfo();
        $post["node_id"] = $user_info["node_id"];
        $model = new \app\common\model\Template();
        $model->save($post);
        if ($model->id) {
            return $this->resultArray("添加成功");
        }
        return $this->resultArray('failed', '添加失败');
    }

    /**
     * 获取所有模板
     * @return array
     */
    public function getTemplate()
    {
        $field = "id,name as text,node_id,industry_name";
        return $this->getList((new \app\common\model\Template), $field);
    }

    /**
     * 获取站点模板列表
     * @param $site_id
     * @return array
     */
    public function filelist($site_id, $type)
    {
        $url = "templatelist";
        $site = \app\common\model\Site::get($site_id);
        if ($site) {
            $siteData = $this->curl_get($site->url . "/index.php/$url?list=" . $type);
            //dump($site->url . "/index.php/$url?list=" . $type);die;
            $result = trim($siteData, "\xEF\xBB\xBF");
            $data = json_decode($result, true);
            if ($data['status'] == 'success') {
                return $this->resultArray($data['status'], $data['msg'], $data["filelist"]);
            }
            return $this->resultArray($data['status'], $data['msg']);
        }
        return $this->resultArray('failed', '当前网站未获取到!');
    }

    /**
     * 上传静态文件
     * $flag add update
     * $list html static
     * $filename 文件名
     */
    public function uploadtemplatestatic()
    {
        $request = Request::instance();
        $siteurl = 'manageTemplateFile';
        $site_id = \request()->post('site_id');
        $flag = \request()->post('flag');
        $filename = \request()->post('filename');
        if (empty($filename)) {
            $filename = \request()->file('file')->getInfo()['name'];
        }
        $list = \request()->post('file_type');
        $url = $this->uploadImg('templatestatic/');
        $site = \app\common\model\Site::get($site_id);
        if ($site) {
            $siteData = $this->curl_get($site->url . "/index.php/$siteurl?osspath=" . $url . "&flag=" . $flag . "&filename=" . $filename . "&list=" . $list);
            $result = trim($siteData, "\xEF\xBB\xBF");
            $data = json_decode($result, true);
            if ($data['status'] == 'success') {
                return $this->resultArray($data['status'], $data['msg']);
            }
            return $this->resultArray('failed', '更新失败');
        }
    }


    /**
     *修改静态文件
     */

    public function updatestatic()
    {
        $request = Request::instance();
        $site_id = $request->post("site_id");
        $filename = $request->post("filename");
        $content = $request->post("content");
        $flag = \request()->post('flag');
        $siteurl = "manageTempalteFile";
        $site = \app\common\model\Site::get($site_id);
        $list = \request()->post('file_type');
        $url = $this->uploadTstatic('templatestatic/', 'file', $content);
        $this->open_start('修改成功');
        if ($site) {
            $this->curl_get($site->url . "/index.php/$siteurl?osspath=" . $url . "&flag=" . $flag . "&filename=" . $filename . "&list=" . $list);
        }
    }


    /**
     * 模板管理 读取模板
     * @return \think\Response
     */
    public function templateRead()
    {
        $request = Request::instance();
        $site_id = $request->post("site_id");
        $list = $request->post("file_type");
        $name = $request->post("name");
        $url = 'templateFileRead';
        $site = \app\common\model\Site::get($site_id);
        if ($site) {
            $siteData = $this->curl_get($site->url . "/index.php/$url?filename=" . $name . "&list=" . $list);
            //print_r($site->url . "/index.php/$url?filename=" . $name . "&list=" . $list);die;
            $result = trim($siteData, "\xEF\xBB\xBF");
            $data = json_decode($result, true);
            //print_r($data);die;
            return $this->resultArray($data['status'], $data['msg'], ["content" => $data["content"], "filename" => $data["filename"]]);
        }
        return $this->resultArray('failed', '当前网站未获取到!');
    }


    /**
     * 显示指定的资源
     * @param  int $id
     * @return \think\Response
     */
    public function readFile()
    {
        return $this->savetemplate();
    }

    public function renameTemp(){
        $request = Request::instance();
        $site_id = $request->post("site_id");
        $list = $request->post("file_type");
        $oldname = $request->post("name");
        $newname = $request->post("newfilename");
        $url = 'templateFileRename';
        $site = \app\common\model\Site::get($site_id);
        if ($site) {
            $siteData = $this->curl_get($site->url . "/index.php/$url?filename=" . $oldname . "&list=" . $list."&newfilename".$newname);
            //print_r($site->url . "/index.php/$url?filename=" . $name . "&list=" . $list);die;
            $result = trim($siteData, "\xEF\xBB\xBF");
            $data = json_decode($result, true);
            return $this->resultArray($data['status'], $data['msg']);
        }
        return $this->resultArray('failed', '当前网站未获取到!');

    }


    /**
     * 保存新建的资源
     * @param  \think\Request $request
     * @return \think\Response
     */
    public function addTemp(Request $request)
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
            return $this->resultArray("failed", $validate->getError());
        }
        $tem = new \app\common\model\Template();
        if (!$tem->allowField(true)->save($post)) {
            return $this->resultArray("failed", "添加失败");
        }
        return $this->resultArray("添加成功!!");
    }


    /**
     * 保存更新的资源
     *
     * @param  \think\Request $request
     * @param  int $id
     * @return \think\Response
     */
    public function oldTemplate(Request $request, $id)
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
            return $this->resultArray("failed", $validate->getError());
        }
        if (!\app\common\model\Template::update($put, ["id" => $id])) {
            return $this->resultArray("failed", "修改失败!");
        }
        return $this->resultArray("修改成功!");
    }


    /**
     * 上传嵌套后的模板文件
     * @return array
     */
    public function uploadPHPTemplate()
    {
        $data = $this->uploadImg("template/");
        if ($data['status']) {
            return $this->resultArray($data['status'], '上传成功', $data['url']);
        } else {
            return $this->resultArray('failed', '上传失败');
        }
    }

    /**
     * 上传原始模板
     * @return array
     * @param $src 原始zip目录
     * @param $obj 解压缩后的目录
     * @param $uploadpath 解压的目录
     */
    public function uploadOldtemplate()
    {
        $request = Request::instance();
        $template = $request->file("file");
        $path = "/upload/srctemplate/";
        $info = $template->move(ROOT_PATH . "public" . $path);
        if ($info) {
            $uploadpath = "/upload/zipsrctemplate/";
            $src = ROOT_PATH . "public" . $path . $info->getSaveName();
            $obj = ROOT_PATH . 'public' . $uploadpath;
            $url = $this->ZipArchive($src, $obj, $uploadpath);
            $data = $this->uploadTempObj("template/" . $info->getSaveName(), $src);
            if ($data['status']) {
                $dataurl = [
                    'url' => $data['url'],
                    'data' => $url
                ];
                return $this->resultArray('上传成功', '', $dataurl);
            } else {
                return $this->resultArray('上传失败', 'failed');
            }
        }
        return $this->resultArray("上传失败", "failed");

    }


    /**
     * 保存新建的资源
     *
     * @param  \think\Request $request
     * @return \think\Response
     */
    public function savetemplate()
    {
        $request = Request::instance();
        $site_id = $request->post("site_id");
        $filename = $request->post("filename");
        $info = pathinfo($filename);
        $content = $request->post("content");
        $flag = $request->post("flag");
        $siteurl = "manageTemplateFile";
        $site = \app\common\model\Site::get($site_id);
        $list = \request()->post('file_type');
        $url = $this->uploadTstatic($info['extension'], $content);
        if ($site) {
            $siteData = $this->curl_get($site->url . "/index.php/$siteurl?osspath=" . $url . "&flag=" . $flag . "&filename=" . $filename . "&list=" . $list);
            $result = trim($siteData, "\xEF\xBB\xBF");
            $data = json_decode($result, true);
            return $this->resultArray($data['status'], $data['msg']);
        }
    }

}