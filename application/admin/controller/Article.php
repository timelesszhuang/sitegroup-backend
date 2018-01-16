<?php

namespace app\admin\controller;

use app\common\controller\Common;
use app\common\traits\Osstrait;
use OSS\OssClient;
use think\Cache;
use think\Config;
use think\Db;
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
        $data = $this->getread((new \app\admin\model\Article), $id);
        $data['data']['tags'] = implode(',',array_filter(explode(',',$data['data']['tags'])));
        return $data;
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
            ["tag_id", "require", "请选择标签"],
        ];
        $validate = new Validate($rule);
        $data = $request->post();
        $user = $this->getSessionUser();
        $data['node_id'] = $user['user_node_id'];
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), "failed");
        }
        if(isset($data['tag_id'])&&is_array($data['tag_id'])){
            $data['tags']=','.implode(',',$data['tag_id']).',';
        }else{
            $data['tags']="";
        }
        unset($data['tag_id']);
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
            ["tag_id", "require", "请选择标签"],
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
                //如果是引用的图片链接的话 不需要生成缩略图名 只有oss的形式才会
                if (strpos($data['thumbnails'], $url) !== false) {
                    $filetype = $this->analyseUrlFileType($data["thumbnails"]);
                    $filename = $this->formUniqueString();
                    //缩略图名称 用于静态化到其他地方时候使用
                    $data["thumbnails_name"] = $filename . "." . $filetype;
                }
            }
        }
        if(isset($data['tag_id'])&&is_array($data['tag_id'])){
            $data['tags']=','.implode(',',$data['tag_id']).',';
        }else{
            $data['tags']="";
        }
        unset($data['tag_id']);
        if (!(new \app\admin\model\Article)->save($data, ["id" => $id])) {
            return $this->resultArray('修改失败', 'failed');
        }
        //先返回给前台 然后去后端 重新生成页面 这块暂时有问题
        $this->open_start('正在修改中');
        //找出有这篇  文章的菜单
        $articletype_id = $data['articletype_id'];
        $sitedata = $this->getArticleSite($articletype_id);
        if (array_key_exists('status', $sitedata)) {
            return $sitedata;
        }
        foreach ($sitedata as $kk => $vv) {
            $send = [
                "id" => $data['id'],
                "searchType" => 'article',
            ];
            $this->curl_post($vv['url'] . "/index.php/generateHtml", $send);
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
        $data = $this->uploadImg("article/");
        if ($data['status']) {
            $data["msg"] = "上传成功";
            return $data;
        } else {
            return $this->resultArray('上传失败', 'failed');
        }
    }

    /**
     * csv上传到 oss相关操作
     * @access public
     */
    public function csvupload()
    {
        $request = Request::instance();
        $file = $request->file('file');
        $localpath = ROOT_PATH . "public/upload/";
        $fileInfo = $file->move($localpath);
        $localfilepath = $localpath . $fileInfo->getSaveName();
        $data = $this->uploadObj("article/csv/" . $fileInfo->getSaveName(), $localfilepath);
        if ($data['status']) {
            $data["msg"] = "上传成功";
            return $data;
        } else {
            return $this->resultArray('上传失败', 'failed');
        }
    }


    /**
     * 获取一篇文章对应的站点 可能是多个站
     * @param $articletype_id
     * @return array|false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function getArticleSite($articletype_id)
    {
        //首先取出选择该文章分类的菜单 注意有可能是子菜单
        $where['flag'] = 3;
        $model_menu = (new \app\admin\model\Menu());
        $menu = $model_menu->where(function ($query) use ($where) {
            $query->where($where);
        })->where(function ($query) use ($articletype_id) {
            $query->where('type_id', ['=', $articletype_id], ['like', "%,$articletype_id,%"], 'or');
        })->select();
        if (!$menu) {
            return $this->resultArray('文章分类没有菜单选中页面，暂时不能预览。', 'failed');
        }
        //一个菜单有可能被多个站点选择 site表中只会存储第一级别的菜单 需要找出当前的pid=0的父级菜单
        $pid = [];
        foreach ($menu as $k => $v) {
            $path = $v['path'];
            //该菜单的跟
            if ($path) {
                //获取第一级别的菜单
                $pid[] = array_values(array_filter(explode(',', $path)))[0];
            } else {
                $pid[] = $v['id'];
            }
        }
        $pid = array_unique($pid);
        //查询选择当前菜单的站点相关信息
        $map = '';
        foreach ($pid as $k => $v) {
            $permap = " menu like ',%$v%,' ";
            if ($k == 0) {
                $map = $permap;
                continue;
            }
            $map .= ' or ' . $permap;
        }
        $sitedata = \app\admin\model\Site::where($map)->field('id,site_name,url')->select();
        return $sitedata;
    }


    /**
     * @return array
     * 文章预览页面
     */
    public function articleshowhtml()
    {
        $data = $this->request->post();
        $articletype_id = $data['articletype_id'];
        $sitedata = $this->getArticleSite($articletype_id);
        if (array_key_exists('status', $sitedata)) {
            return $sitedata;
        }
        foreach ($sitedata as $kk => $vv) {
            $showhtml[] = [
                'url' => $vv['url'] . '/preview/article/' . $data['id'] . '.html',
                'site_name' => $vv['site_name'],
            ];
        }
        if (!empty($showhtml)) {
            return $this->resultArray('', '', $showhtml);
        } else {
            return $this->resultArray('当前文章对应的菜单页面没有站点选择，暂时不能预览。', 'failed');
        }
    }

    /**
     * csv导入 文章
     * @access public
     */
    public function csvimport()
    {
        $sql = new \app\admin\model\Article();
        $data = $this->request->post();
        $url = $data['csvupload'];
        $article_type_id = $data['articletype_id'];
        $article_type_name = $data['articletype_name'];
        $csv = $this->getCsvFromOSS($url);
        $values = [];
        $user = $this->getSessionUser();
        $result = [];
        Db::startTrans();
        try {
            if (is_array($csv))
                foreach ($csv as $key => $item) {
                    $value = [];
                    if ($key == 0) continue;
                    if (count($item) > 1) {
                        $value['title'] = $item[0];
                        $value['content'] = $item[1];
                        if ($value['title'] == "") {
                            $result['error'][] = ['key' => $key, "message" => '第' . ($key) . '条没有标题'];
                            continue;
                        }
                        if ($value['content'] == "") {
                            $result['error'][] = ['key' => $key, "message" => '第' . ($key) . '条没有内容'];
                            continue;
                        }
                        $value['auther'] = $item[2];
                        $value['come_from'] = $item[3];
                        $value['readcount'] = $item[4] == '' ? rand(100, 10000) : $item[4];
                        $value['summary'] = $item[5] == '' ? mb_substr(trim(strip_tags(str_replace('&nbsp;', '', $value['content']))), 0, 40 * 3, 'utf-8') : $item[5];
                        $value['keywords'] = $item[6];
                        $value['articletype_name'] = $article_type_name;
                        $value['articletype_id'] = $article_type_id;
                        $value['node_id'] = $user["user_node_id"];
                        $value['create_time'] = time();
                        $value['update_time'] = time();
                        $values[] = $value;
                    }
                    if (count($values) >= 30) {
                        $sql->insertAll($values);
                        $values = [];
                    }
                }
            if (count($values) > 0) {
                $sql->insertAll($values);
            }
            // 提交事务
            Db::commit();
            return $this->resultArray("添加成功", '', $result);
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return $this->resultArray("添加失败", 'failed');
        }
    }

    /**
     * 从OSS获取CSV文本信息
     * @param $url
     * @return bool|string
     */
    public function getCsvFromOSS($url)
    {
        $file = fopen($url, 'r');
        $datas = [];
        //编码格式
        $encoding = "";
        while ($data = fgetcsv($file)) {
            if ($encoding === "") {
                $encoding = mb_detect_encoding($data[0], array("ASCII", 'UTF-8', "GB2312", "GBK", 'BIG5'));
            }
            if ($encoding !== "" && $encoding)
                foreach ($data as $key => $value) {
                    $data[$key] = iconv($encoding, 'UTF-8', $value);
                }
            $datas = array_merge($datas, [$data]);
        }
        return $datas;
    }

}
