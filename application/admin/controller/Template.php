<?php

namespace app\admin\controller;

use app\common\controller\Common;
use think\Request;
use think\Validate;
use app\common\traits\Obtrait;
use app\common\traits\Osstrait;

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
        $user = $this->getSessionUser();
        $where["node_id"] = [["=", $user["user_node_id"]], ["=", 0], "or"];
        $data = (new \app\admin\model\Template())->getTemplate($request["limit"], $request["rows"], $where);
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
        return $this->getread((new \app\admin\model\Template), $id);
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

        return $this->publicUpdate((new \app\admin\model\Template()), $data, $id);
    }

    /**
     * 删除指定资源 模板暂时不支持删除操作
     * @param  int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $template = new \app\admin\model\Template();
        $user = $this->getSessionUser();
        $where["parent_id"] = $id;
        $where["node_id"] = $user["user_node_id"];
        if ($template->where(["id" => $id, "node_id" => $user["user_node_id"]])->delete()) {
            return $this->resultArray('删除成功', 'failed');
        }
        return $this->resultArray('删除成功');
    }

    /**
     * 上传关键词文件文件
     * @return array
     */
    public function uploadTemplate()
    {
        $data = $this->uploadImg("template/");
        if($data['status']){
            return $this->resultArray('上传成功',$data['status'],$data['url']);
        }else{
            return $this->resultArray('上传失败', 'failed');
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
        ];
        $validate = new Validate($rule);
        if (!$validate->check($post)) {
            return $this->resultArray($validate->getError(), 'failed');
        }
        $user = $this->getSessionUser();
        $post["node_id"] = $user["user_node_id"];
        $model = new \app\admin\model\Template();
        $model->save($post);
        if ($model->id) {
            return $this->resultArray("添加成功");
        }
        return $this->resultArray('添加失败', 'failed');
    }

    /**
     * 获取所有模板
     * @return array
     */
    public function getTemplate()
    {
        $field = "id,name as text,node_id,industry_name";
        return $this->getList((new \app\admin\model\Template), $field);
    }

    /**
     * 获取站点模板列表
     * @param $site_id
     * @return array
     */
    public function filelist($site_id)
    {
        $url = "templatelist";
        $site = \app\admin\model\Site::get($site_id);
//        dump($site->url."/index.php/$url?site_id=".$site_id);die;
        if ($site) {
            $siteData = $this->curl_get($site->url . "/index.php/$url?site_id=" . $site_id);
            $result = trim($siteData, "\xEF\xBB\xBF");
            $data = json_decode($result, true);
//            dump($data);die;
            return $this->resultArray($data['msg'], '', $data["filelist"]);
        }
        return $this->resultArray('当前网站未获取到!', 'failed');
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function templateRead($site_id, $name)
    {
        $url = "templateread";
        $site = \app\admin\model\Site::get($site_id);
        if ($site) {
            $siteData = $this->curl_get($site->url . "/index.php/$url?site_id=" . $site_id . "&filename=" . $name);
//            dump($site->url."/index.php/$url?site_id=".$site_id."&filename=".$name);die;
            $result = trim($siteData, "\xEF\xBB\xBF");
            $data = json_decode($result, true);
//            $data=json_decode($siteData,true);
            return $this->resultArray($data['msg'], '', ["content" => $data["content"], "filename" => $data["filename"]]);
        }
        return $this->resultArray('当前网站未获取到!', 'failed');
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request $request
     * @return \think\Response
     */
    public function save($site_id, $name)
    {
        $request = Request::instance();
        $content = $request->post("content");
        $url = "templateupdate";
        $site = \app\admin\model\Site::get($site_id);
        if ($site) {
            $send = [
                "site_id" => $site_id,
                "filename" => $name,
                "content" => $content
            ];
            $siteData = $this->curl_post($site->url . "/index.php/" . $url, $send);
            $data = json_decode($siteData, true);
            return $this->resultArray($data['msg'], $data["status"]);
        }
        return $this->resultArray('当前网站未获取到!', 'failed');
    }

    /**
     * 显示指定的资源
     * @param  int $id
     * @return \think\Response
     */
    public function readFile($site_id, $name)
    {
        $request = Request::instance();
        $content = $request->post("content");
        $url = "templateadd";
        $site = \app\admin\model\Site::get($site_id);
        if ($site) {
            $send = [
                "site_id" => $site_id,
                "filename" => $name,
                "content" => $content
            ];
            $siteData = $this->curl_post($site->url . "/index.php/" . $url, $send);
            $data = json_decode($siteData, true);
            return $this->resultArray($data['msg'], $data["status"]);
        }
        return $this->resultArray('当前网站未获取到!', 'failed');
    }
}