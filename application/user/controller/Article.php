<?php

namespace app\user\controller;

use app\admin\controller\Articletype;
use app\common\controller\Common;
use think\Request;
use think\Session;
use think\Validate;
use app\common\traits\Obtrait;
use Closure;
use OSS\OssClient;
use app\common\traits\Osstrait;
class Article extends Common
{
    use Obtrait;
    use Osstrait;

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $request = $this->getLimit();
        $node_id = $this->getSiteSession('login_site');
        $where = [];
        $where["node_id"] = $node_id["node_id"];
        $where["site_id"] = $this->getSiteSession('website')["id"];
        $data = (new \app\admin\model\Article())->getArticle($request["limit"], $request["rows"], $where);
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
            ["title", "require", "请输入标题"],
            ["content", "require", "请输入内容"],
            ["articletype_id", "require", "请选择文章分类"],
        ];
        $validate = new Validate($rule);
        $data = $request->post();
        $data['node_id'] = $this->getSiteSession('login_site')["node_id"];
        $data["site_id"] = $this->getSiteSession('website')["id"];
        $data["site_name"] = $this->getSiteSession('website')["site_name"];
        $data['is_sync'] = '10';
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), "failed");
        }
        if(!empty($data["summary"])){
            $data['summary'] = $this->utf8chstringsubstr($data['content'], 75 * 3);
        }
        if (!\app\admin\model\Article::create($data)) {
            return $this->resultArray("添加失败", "failed");
        }
        return $this->resultArray("添加成功");
    }

    /**
     * 显示指定的资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function read($id)
    {
        return $this->getread((new \app\admin\model\Article), $id);
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
            ["title", "require", "请输入标题"],
            ["content", "require", "请输入内容"],
            ["articletype_id", "require", "请选择文章分类"],
        ];
        $validate = new Validate($rule);
        $data = $request->post();
        $data['node_id'] = $this->getSiteSession('login_site')["node_id"];
        $data["site_id"] = $this->getSiteSession('website')["id"];
        $data["site_name"] = $this->getSiteSession('website')["site_name"];
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), "failed");
        }
        //如果summary是空的话 自动生成
        if (empty($data["summary"])) {
            $data['summary'] = $this->utf8chstringsubstr($data['content'], 40 * 3);
        }
        // 如果传递了缩略图的话 比对删除
        if ($data["thumbnails"]) {
            $id_data = \app\admin\model\Article::get($id);
            if (empty($id_data)) {
                return $this->resultArray("获取数据失败", 'failed');
            }
            //比对两个缩略图的地址 删除原始 添加thumbnails_name
            if ($data["thumbnails"] == $id_data->thumbnails) {
                //删除
                $this->ossDeleteObject($id_data->thumbnails);
                //获取后缀
                $file_suffix=$this->analyseUrlFileType($data["thumbnails"]);
                //缩略图名称
                $data["thumbnails_name"] = $this->formUniqueString().".".$file_suffix;
            }
        }
        return $this->publicUpdate((new \app\admin\model\Article), $data, $id);
    }

    /**
     * 删除指定资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        return $this->deleteRecord((new \app\admin\model\Article), $id);
    }

    /**
     * 获取文章类型
     * @return array
     *//**
 * 获取站点文章分类
 * @return array
 */
    public function getArticleType()
    {


        $where = [];
        $wh['id'] = $this->request->session()['website']['id'];
        $Site = new \app\admin\model\Site();
        $menuid = $Site->where($wh)->field('menu')->find()->menu;
        $Menuid = explode(',',$menuid);
        $where['id'] = $Menuid;
        $menu = new \app\admin\model\Menu();
        $whe['flag'] = 3;
        $data = $menu->where('id','in',$Menuid)->where($whe)->field('type_id,type_name,tag_name')->select();
        foreach ($data as$k=>$v){
            $v['text'] = $v['type_name'].'['.$v['tag_name'].']';
            $v['id'] = $v['type_id'];
        }
        return $this->resultArray('','',$data);
    }

    /**
     * 获取错误信息
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getErrorInfo()
    {
        $site_id = Session::get("website")["id"];
        $node_id = Session::get('login_site')["node_id"];
        $request = $this->getLimit();
        $where = [
            "node_id" => $node_id,
            "site_id" => $site_id
        ];
        $data = (new \app\common\model\SiteErrorInfo())->getAll($request["limit"], $request["rows"], $where);
        return $this->resultArray('', '', $data);
    }

    /**
     * 修改错误信息status
     * @param $id
     * @return array
     */
    public function changeErrorStatus($id)
    {
        $user = (new Common())->getSessionUser();
        $node_id = Session::get('login_site')["node_id"];
        $site_id = Session::get("website")["id"];
        $where = [
            "id" => $id,
            "node_id" => $node_id,
            "site_id" => $site_id
        ];
        $site = \app\common\model\SiteErrorInfo::where($where)->find();
        $site->status = 10;
        $site->update_time = time();
        if (!$site->save()) {
            return $this->resultArray('修改失败', 'failed');
        }
        return $this->resultArray('修改成功');
    }

    /**
     * 获取当前节点有多少没有查看的日志
     * @return array
     */
    public function getErrorStatus()
    {
        $user = $this->getSessionUser();
        $site_id = Session::get("website")["id"];
        $where = [
            "node_id" => $user["user_node_id"],
            "status" => 20,
            "site_id" => $site_id
        ];
        $count = (new \app\common\model\SiteErrorInfo())->where($where)->count();
        if ($count < 1) {
            $count = "无";
        }
        return $this->resultArray('', '', $count);
    }

    public function siteGetCurl($id, $name)
    {
        $func = function () use ($id) {
            $user = $this->getSessionUser();
            $nid = $user["user_node_id"];
            $where = [
                "id" => $id,
                "node_id" => $nid
            ];
            $site = \app\admin\model\Site::where($where)->find();
            if (is_null($site)) {
                return $this->resultArray('发送失败,无此记录!', 'failed');
            }
            return $site->url;
        };
        return $this->callGetClosure($func, $name);
    }

    /**
     * 统一站点get调用接口
     * @param Closure $closure
     * @param $name
     */
    public function callGetClosure(closure $closure, $name)
    {
        $url = $closure();
        list($newUrl, $msg) = $this->getSwitchUrl($url, $name);
        //断开前台请求
        $this->open_start($msg);
        //发送curl get请求
        $this->curl_get($newUrl);
    }

    /**
     * 根据name获取指定的url和msg
     * @param $name
     * @return array
     */
    public function getSwitchUrl($url, $name)
    {
        $NewUrl = '';
        $msg = '';
        switch ($name) {
            case "aKeyGeneration":
                $msg = "正在一键生成...";
                $NewUrl = $url . "/allstatic";
                break;
            case "generatIndex":
                $msg = "正在生成首页...";
                $NewUrl = $url . "/indexstatic";
                break;
            case "generatArticle":
                $msg = "正在生成文章页...";
                $NewUrl = $url . "/artilestatic";
                break;
            case "generatMenu":
                $msg = "正在生成栏目...";
                $NewUrl = $url . "/menustatic";
                break;
            case "clearCache":
                $msg = "正在清除...";
                $NewUrl = $url . "/clearCache";
                break;
        }
        return [$NewUrl, $msg];
    }

    /**
     * 统计文章
     * @return array
     */
    public function ArticleCount()
    {
        $count = [];
        $name = [];
        foreach ($this->countArticle() as $item) {
            $count[] = $item["count"];
            $name[] = $item["name"];
        }
        $arr = ["count" => $count, "name" => $name];
        return $this->resultArray('', '', $arr);
    }

    public function countArticle()
    {
        $user = $this->getSessionUser();
        $where = [
            'node_id' => $user["user_node_id"],
        ];
        $articleTypes = \app\admin\model\Articletype::get($where);
        foreach ($articleTypes as $item) {
            yield $this->foreachArticle($item);
        }
    }

    public function foreachArticle($articleType)
    {
        $count = \app\admin\model\Article::where(["articletype_id" => $articleType->id])->count();
        return ["count" => $count, "name" => $articleType->name];

    }



}
