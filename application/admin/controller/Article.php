<?php

namespace app\admin\controller;

use app\common\controller\Common;
use app\common\traits\Osstrait;
use OSS\OssClient;
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
        //如果前台有传递summary就使用 否则自动提取
        if (empty($data['summary'])) {
            $data['summary'] = $this->utf8chstringsubstr($data['content'], 40 * 3);
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
                $file_suffix = $this->analyseUrlFileType($data["thumbnails"]);
                //缩略图名称
                $data["thumbnails_name"] = $this->formUniqueString() . "." . $file_suffix;
            }
        }

        if (!(new \app\admin\model\Article)->save($data, ["id" => $id])) {
            return $this->resultArray('修改失败', 'failed');
        }
        //先返回给前台 然后去后端 重新生成页面
        $this->open_start('正在修改中');
        $where['type_id'] = $data['articletype_id'];
        $where['flag'] = 3;
        $menu = (new \app\admin\model\Menu())->where($where)->select();
        $user = $this->getSessionUser();
        $wh['node_id'] = $user['user_node_id'];
        $sitedata = \app\admin\model\Site::where($wh)->select();
        $arr = [];
        foreach ($menu as $k => $v) {
            $arr[] = $v['id'];
            foreach ($sitedata as $kk => $vv) {
                $a = strstr($vv["menu"], "," . $v["id"] . ",");
                if ($a) {
                    $Site = new \app\admin\model\Site();
                    $dat = $Site->where('id', 'in', $vv['id'])->field('url')->select();
                    foreach ($dat as $key => $value) {
                        $send = [
                            "id" => $data['id'],
                            "searchType" => 'article',
                            "type" => $data['articletype_id']
                        ];
                        $this->curl_post($value['url'] . "/index.php/generateHtml", $send);
                    }
                }
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
     * 同步文章
     * @param $id
     * @return array
     */
    public function syncArticle()
    {
        $is_sync = $this->request->post('is_sync');
        $id = $this->request->post("id");
        $user = $this->getSessionUser();
        $where["node_id"] = $user["user_node_id"];
        $where["id"] = $id;
        if ((new \app\admin\model\Article)->save(["is_sync" => $is_sync], $where)) {
            return $this->resultArray("修改成功");
        }
        return $this->resultArray("添加失败", 'failed');
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

}
