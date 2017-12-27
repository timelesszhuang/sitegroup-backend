<?php

namespace app\user\controller;

use app\admin\controller\Articletype;
use app\admin\model\Menu;
use app\admin\model\Site;
use app\common\controller\Common;
use think\Config;
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
        $site_id['id'] = $this->getSiteSession('website')["id"];
        $menu = (new Site())->where($site_id)->field('menu')->find();
        $menuid = array_filter(explode(",", $menu->menu));
        $where['id'] = ['in', $menuid];
        $where['flag']=3;
        $menudata = (new Menu())->where($where)->field('type_id')->select();
        foreach ($menudata as $k=>$v){
          $arr[] = $v['type_id'];
        };
        $aricle = [];
        $aricle['articletype_id'] = ['in',  $arr];
        $articleid = $this->request->get('article_type');
        $title = $this->request->get('title');
        if (!empty($articleid)) {
            $aricle['articletype_id'] =  $articleid;
        }
        if (!empty($title)) {
            $aricle['title'] = ["like", "%$title%"];
        }
        $articledata = (new \app\admin\model\Article())->where($aricle)->limit($request["limit"], $request["rows"])->order('id desc')->select();
        $count = (new \app\admin\model\Article())->where($aricle)->count();
        $data = [
            "total" => $count,
            "rows" => $articledata
        ];
        return $this->resultArray('','',$data);
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
        //dump($data);die;
        $data['node_id'] = $this->getSiteSession('login_site')["node_id"];
        $data["site_id"] = $this->getSiteSession('website')["id"];
        $data["site_name"] = $this->getSiteSession('website')["site_name"];
        $data['is_sync'] = '10';
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), "failed");
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
        // 如果传递了缩略图的话 比对删除
        if ($data["thumbnails"]) {
            $id_data = \app\admin\model\Article::get($id);
            if (empty($id_data)) {
                return $this->resultArray("获取数据失败", 'failed');
            }
            //比对两个缩略图的地址 删除原始 添加thumbnails_name
            if ($data["thumbnails"] != $id_data->thumbnails) {
                //缩略图有可能是从文章中提取的 所以可能为非 aliyun oss 的链接
                $endpoint = Config::get('oss.endpoint');
                $bucket = Config::get('oss.bucket');
                $url = sprintf("https://%s.%s/", $bucket, $endpoint);
                if (strpos($id_data->thumbnails, $url) !== false) {
                    //表示之前缩略图是oss的 现在新添加的一定是oss的
                    $this->ossDeleteObject($id_data->thumbnails);
                }
                //删除
                //获取后缀
                $filetype = $this->analyseUrlFileType($data["thumbnails"]);
                $filename = $this->formUniqueString();
                //缩略图名称 用于静态化到其他地方时候使用
                $data["thumbnails_name"] = $filename . "." . $filetype;
            }
        }
        if (!(new \app\admin\model\Article)->save($data, ["id" => $id])) {
            return $this->resultArray('修改失败', 'failed');
        }
        $this->open_start('正在修改中');
        $where["id"] = $this->getSiteSession('website')["id"];
        // dump($where);die;
        $Site = (new Site())->where($where)->field('url')->find();
        //dump($Site['url']);die;
        $send = [
            "id" => $data['id'],
            "searchType" => 'article',
            "type" => $data['articletype_id'],
        ];
        $this->curl_post($Site['url'] . "/index.php/generateHtml", $send);
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
     */
    /**
     * 获取站点文章分类
     * @return array
     */
    public function getArticleType()
    {
        $where = [];
        $Menuid = $this->request->session()['website']['menu'];
        $menu = new \app\admin\model\Menu();
        $whe['flag'] = 3;
        $data = $menu->where($whe)->where('id', 'in', $Menuid);
        foreach (array_filter(explode(',',$Menuid)) as $menu_id){
            $data=$data->whereOr('path','like',"%,$menu_id,%");
        }
        $data=$data->field('type_id,type_name,tag_name');
        $data=$data->select();
        $type_ids=[];
        foreach ($data as $k => $v) {
            foreach(array_filter(explode(',',$v['type_id'])) as $value){
                $type_ids[$value]=1;
            }
        }
        $type_ids=array_keys($type_ids);
        $data =(new \app\admin\model\Articletype)->alias('type')->field('type.id,name,tag_id,tag')->join('type_tag','type_tag.id = tag_id')->where('type.id','in',$type_ids)->select();
        $dates=[];
        foreach ($data as$k=>$v){
            $dates[$v['tag']][] = ['id'=>$v['id'],'name'=>$v['name']];
        }
        return $this->resultArray('', '', $dates);
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
     * 图片上传到 oss相关操作
     * @access public
     */
    public function imageupload()
    {
        $dest_dir = 'article/';
        $endpoint = Config::get('oss.endpoint');
        $bucket = Config::get('oss.bucket');
        $request = Request::instance();
        $file = $request->file("file");
        $localpath = ROOT_PATH . "public/upload/";
        $fileInfo = $file->move($localpath);
        $object = $dest_dir . $fileInfo->getSaveName();
        $localfilepath = $localpath . $fileInfo->getSaveName();
        $put_info = $this->ossPutObject($object, $localfilepath);
        unlink($localfilepath);
        $msg = '上传缩略图失败';
        $url = '';
        $status = false;
        if ($put_info['status']) {
            $msg = '上传缩略图成功';
            $status = true;
            $url = sprintf("https://%s.%s/%s", $bucket, $endpoint, $object);
        }
        return [
            'msg' => $msg,
            "url" => $url,
            'status' => $status
        ];
    }

    /**
     * @return array
     * 文章预览
     */
    public function articleshowhtml()
    {
        $id = $this->request->post('id');
        //$where['node_id'] = $this->getSiteSession('login_site')["node_id"];
        $where["id"] = $this->getSiteSession('website')["id"];
        // dump($where);die;
        $Site = (new Site())->where($where)->field('url')->find();
        $showurl = $Site['url'] . '/preview/article/' . $id . '.html';
        //dump($Site['url']);die;
        return $this->resultArray('', '', $showurl);
    }


}
