<?php

namespace app\common\controller;

use app\common\exception\ProcessException;
use app\common\model\Menu;
use app\common\model\Site;
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
use app\common\model\LibraryImgset;
use app\common\model\Article as this_model;

class Article extends CommonLogin
{
    use Obtrait;
    use Osstrait;
    public function __construct()
    {
        parent::__construct();
        $this->model = new this_model();
    }
    /**
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
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
        $user_info = $this->getSessionUserInfo();
        if ($user_info['user_type_name'] == 'node') {
            $where["node_id"] = $user_info["node_id"];
        } else {
            $type_ids = (new Menu())->getSiteTypeIds($user_info['menu'], 3);
            $where['articletype_id'] = ['in', $type_ids];
        }
        $data = $this->model->getArticle($request["limit"], $request["rows"], $where);
        return $this->resultArray($data);
    }

    /**
     * @param $id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function read($id)
    {
        $data = $this->getread((new this_model), $id);
        $data['data']['tags'] = implode(',', array_filter(explode(',', $data['data']['tags'])));
        return $data;
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request $request
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function save(Request $request)
    {
        try {
            $rule = [
                ["title", "require", "请输入标题"],
                ["content", "require", "请输入内容"],
                ["articletype_id", "require", "请选择文章分类"],
//            ["tag_id", "require", "请选择标签"],
            ];
            $validate = new Validate($rule);
            $data = $request->post();
            $user = $this->getSessionUserInfo();
            $data['node_id'] = $user['node_id'];
            if (!$validate->check($data)) {
                Common::processException($validate->getError());
            }
            $library_img_tags = [];
            if (isset($data['tag_id']) && is_array($data['tag_id'])) {
                $library_img_tags = $data['tag_id'];
                $data['tags'] = ',' . implode(',', $data['tag_id']) . ',';
            } else {
                $data['tags'] = "";
            }
            unset($data['tag_id']);
            if (!$this->model->create($data)) {
                Common::processException('添加失败');
            }
            $library_img_set = new LibraryImgset();
            $src_list = $library_img_set->getList($data['content']);
            if ($data['thumbnails']) {
                $src_list[] = $data['thumbnails'];
            }
            $library_img_set->batche_add($src_list, $library_img_tags, $data['title'], 'article');
            return $this->resultArray('success', '添加成功');
        } catch (ProcessException $exception) {
            return $this->resultArray("failed", $exception->getMessage());
        }
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request $request
     * @param  int $id
     * @return array|\think\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function update(Request $request, $id)
    {
        try {
        $rule = [
            ["title", "require", "请输入标题"],
            ["content", "require", "请输入内容"],
            ["articletype_id", "require", "请选择文章分类"],
//            ["tag_id", "require", "请选择标签"],
        ];
        $data = $request->put();
        $validate = new Validate($rule);
        if (!$validate->check($data)) {
            Common::processException($validate->getError());
        }
        // 如果传递了缩略图的话 比对删除
        if ($data["thumbnails"]) {
            $id_data = $this->model->get($id);
            if (empty($id_data)) {
                Common::processException("获取数据失败");
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
        $library_img_tags = [];
        if (isset($data['tag_id']) && is_array($data['tag_id'])) {
            $library_img_tags = $data['tag_id'];
            $data['tags'] = ',' . implode(',', $data['tag_id']) . ',';
        } else {
            $data['tags'] = "";
        }
        unset($data['tag_id']);
        if (!$this->model->save($data, ["id" => $id])) {
            Common::processException('修改失败');
        }


        $library_img_set = new LibraryImgset();
        $src_list = $library_img_set->getList($data['content']);
        if ($data['thumbnails']) {
            $src_list[] = $data['thumbnails'];
        }
        $library_img_set->batche_add($src_list, $library_img_tags, $data['title'], 'article');


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
        } catch (ProcessException $exception) {
            return $this->resultArray("failed", $exception->getMessage());
        }

    }

    /**
     * 删除指定资源
     * @param  int $id
     * @return array
     */
    public function delete($id)
    {
        return $this->deleteRecord($this->model, $id);
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
        $model_menu = (new Menu());
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
        $sitedata = (new Site())->where($map)->field('id,site_name,url')->select();
        return $sitedata;
    }


    /**
     * @return array
     * 文章预览页面
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function articleShowHtml()
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
            return $this->resultArray($showhtml);
        } else {
            return $this->resultArray('failed','当前文章对应的菜单页面没有站点选择，暂时不能预览。' );
        }
    }

    /**
     * csv导入 文章
     * @access public
     */
    public function csvimport()
    {
        $sql = $this->model;
        $data = $this->request->post();
        $url = $data['csvupload'];
        $article_type_id = $data['articletype_id'];
        $article_type_name = $data['articletype_name'];
        $csv = $this->getCsvFromOSS($url);
        $values = [];
        $user = $this->getSessionUserInfo();
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
                        $value['node_id'] = $user["node_id"];
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
            return $this->resultArray( 'success',"添加成功", $result);
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return $this->resultArray('failed', "添加失败");
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
                    $data[$key] = iconv($encoding, 'UTF-8//TRANSLIT//IGNORE', $value);
                }
            $datas = array_merge($datas, [$data]);
        }
        return $datas;
    }

}
