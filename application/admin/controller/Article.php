<?php

namespace app\admin\controller;

use app\common\controller\Common;
use app\common\traits\Osstrait;
use OSS\OssClient;
use think\Cache;
use think\Config;
use think\Session;
use think\Validate;
use think\Request;
use app\common\traits\Obtrait;
use think\View;

class Article extends Common
{
    use Obtrait;
    use Osstrait;

    /**
     * @return array
     */
    public function index()
    {
        $request = $this->getLimit();
        $title = $this->request->get('title');
        $article_type = $this->request->get("article_type");
        $where = [];
        if (!empty($title)) {
            $where["title"] = ["like", "%$title%"];
        }
        if (!empty($article_type)) {
            $where['articletype_id'] = $article_type;
        }
        $user = $this->getSessionUser();
        $where["node_id"] = $user["user_node_id"];
        $data = (new \app\admin\model\Article())->getArticle($request["limit"], $request["rows"], $where);
        return $this->resultArray('', '', $data);
    }

    /**
     * @param $id
     * @return array
     */
    public function read($id)
    {
        return $this->getread((new \app\admin\model\Article), $id);
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
        $user = $this->getSessionUser();
        $data['node_id'] = $user['user_node_id'];
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), "failed");
        }
        if (!\app\admin\model\Article::create($data)) {
            return $this->resultArray("添加失败", "failed");
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
            ["title", "require", "请输入标题"],
            ["content", "require", "请输入内容"],
            ["articletype_id", "require", "请选择文章分类"],
        ];
        $data = $request->put();
        $validate = new Validate($rule);
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), 'failed');
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
        //先返回给前台 然后去后端 重新生成页面 这块暂时有问题
        $this->open_start('正在修改中');
        //找出有这篇  文章的菜单
        $where['type_id'] = $data['articletype_id'];
        $where['flag'] = 3;
        $menu = (new \app\admin\model\Menu())->where($where)->select();
        foreach ($menu as $k => $v) {
            $wh['menu'] = ["like", "%,{$v['id']},%"];
            $sitedata = \app\admin\model\Site::where($wh)->select();
            foreach ($sitedata as $kk => $vv) {
                $send = [
                    "id" => $data['id'],
                    "searchType" => 'article',
                    "type" => $data['articletype_id'],
                ];
                $this->curl_post($vv['url'] . "/index.php/generateHtml", $send);
            }
        }
    }

    /**
     * 删除指定资源
     * @param  int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        return $this->deleteRecord((new \app\admin\model\Article), $id);
    }


    /**
     * 获取错误信息
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getErrorInfo()
    {
        $user = $this->getSessionUser();
        $request = $this->getLimit();
        $where = [
            "node_id" => $user["user_node_id"],
        ];
        $data = (new \app\common\model\SiteErrorInfo())->getAll($request["limit"], $request["rows"], $where);
        return $this->resultArray('', '', $data);
    }

    /**
     * 获取当前节点有多少没有查看的日志
     * @return array
     */
    public function getErrorStatus()
    {
        $user = $this->getSessionUser();
        $where = [
            "node_id" => $user["user_node_id"],
            "status" => 20
        ];
        $count = (new \app\common\model\SiteErrorInfo())->where($where)->count();
        if ($count < 1) {
            $count = "无";
        }
        return $this->resultArray('', '', $count);
    }

    /**
     * 修改错误信息status
     * @param $id
     * @return array
     */
    public function changeErrorStatus($id)
    {
        $user = $this->getSessionUser();
        $where = [
            "id" => $id,
            "node_id" => $user["user_node_id"],
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
     * 预览页面
     */
    public function articleshowhtml()
    {
        $data = $this->request->post();
        $where['type_id'] = $data['articletype_id'];
        $where['flag'] = 3;
        $menu = (new \app\admin\model\Menu())->where($where)->select();
        if (!$menu) {
            return $this->resultArray('当前无法预览', 'failed');
        }
        foreach ($menu as $k => $v) {
            $wh['menu'] = ["like", "%,{$v['id']},%"];
            $sitedata = \app\admin\model\Site::where($wh)->select();
            foreach ($sitedata as $kk => $vv) {
                $showhtml[] = [
                    'url' => $vv['url'] . '/preview/article/' . $data['id'] . '.html',
                    'site_name' => $vv['site_name'],
                ];
            }
            if ($showhtml) {
                return $this->resultArray('', '', $showhtml);
            } else {
                return $this->resultArray('当前无法预览', 'failed');
            }
        }

    }

}
