<?php

namespace app\common\controller;

use app\common\exception\ProcessException;
use app\common\model\Menu;
use app\common\model\Site;
use think\Model;
use think\Request;
use think\Validate;
use app\common\traits\Obtrait;
use app\common\traits\Osstrait;
use app\common\model\LibraryImgset;

class Question extends CommonLogin
{
    use Obtrait;
    use Osstrait;

    public function __construct()
    {
        parent::__construct();
        $this->model = new \app\common\model\Question();
    }

    /**
     * 显示资源列表
     *
     * @param Request $request
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author jingzheng
     */
    public function index(Request $request)
    {
        $limits = $this->getLimit();
        $content = $request->get('content');
        $type_id = $request->get("type_id");
        $where = [];
        if (!empty($content)) {
            $where['question'] = ["like", "%$content%"];
        }
        if (!empty($type_id)) {
            $where['type_id'] = $type_id;
        }
        $user = $this->getSessionUserInfo();
        if ($user['user_type_name'] == 'node') {
            $where["node_id"] = $user["node_id"];
        } else {
            $type_ids = (new Menu())->getSiteTypeIds($user['menu'], 2);
            $where['type_id'] = ['in', $type_ids];
        }
        return $this->resultArray($this->model->getAll($limits['limit'], $limits['rows'], $where));
    }

    /**
     * 保存新建的资源
     *
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * @author jingzheng
     */
    public function save()
    {
        try {
            $rule = [
                ['question', "require", "请填写问题"],
                ['content_paragraph', 'require', "请填写答案"],
                ["type_id", "require", "请选择分类id"],
                ["type_name", "require", "请选择分类名称"]
            ];
            $validate = new Validate($rule);
            $data = $this->request->post();
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
            if (!empty($data['flag'])) {
                $data['flag'] = ',' . implode(',', $data['flag']) . ',';
            } else {
                $data['flag'] = '';
            }
            unset($data['tag_id']);
            $data["node_id"] = $this->getSessionUserInfo()['node_id'];
            $library_img_set = new LibraryImgset();
            $src_list = $library_img_set->getList($data['content_paragraph']);
            if (isset($data['thumbnails']) && $data['thumbnails']) {
                $src_list[] = $data['thumbnails'];
            }
            $library_img_set->batche_add($src_list, $library_img_tags, $data['question'], 'question');
            if (!$this->model->create($data)) {
                Common::processException('添加失败');
            }
            return $this->resultArray('添加成功');
        } catch (ProcessException $e) {
            return $this->resultArray('failed', $e->getMessage());
        }
    }

    /**
     * 显示指定的资源
     *
     * @param  int $id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author jingzheng
     */
    public function read($id)
    {
        $data = $this->getread($this->model, $id);
        $data['data']['tags'] = implode(',', array_filter(explode(',', $data['data']['tags'])));
        $data['data']['flag'] = implode(',', array_filter(explode(',', $data['data']['flag'])));
        return $data;
    }

    /**
     * 保存更新的资源
     * @param  int $id
     * @return array|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * @author jingzheng
     */
    public function update($id)
    {
        $rule = [
            ['question', "require", "请填写问题"],
            ['content_paragraph', 'require', "请填写答案"],
            ["type_id", "require", "请选择分类id"],
            ["type_name", "require", "请选择分类名称"]
        ];
        $validate = new Validate($rule);
        $data = $this->request->put();
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), 'failed');
        }
        $library_img_tags = [];
        if (isset($data['tag_id']) && is_array($data['tag_id'])) {
            $library_img_tags = $data['tag_id'];
            $data['tags'] = ',' . implode(',', $data['tag_id']) . ',';
        } else {
            $data['tags'] = "";
        }
        if (!empty($data['flag'])) {
            $data['flag'] = ',' . implode(',', $data['flag']) . ',';
        } else {
            $data['flag'] = '';
        }
        unset($data['tag_id']);
        $this->publicUpdate($this->model, $data, $id);
        $library_img_set = new LibraryImgset();
        $src_list = $library_img_set->getList($data['content_paragraph']);
        if (isset($data['thumbnails']) && $data['thumbnails']) {
            $src_list[] = $data['thumbnails'];
        }
        $library_img_set->batche_add($src_list, $library_img_tags, $data['question'], 'article');
        $this->open_start('正在修改中');
        $sitedata = $this->getQuestionSite($data['type_id']);
        if (array_key_exists('status', $sitedata)) {
            return $sitedata;
        }
        foreach ($sitedata as $kk => $vv) {
//            $send = [
//                "id" => $data['id'],
//                "searchType" => 'question',
//            ];
//            $this->curl_post($vv['url'] . "/index.php/generateHtml", $send);
            $this->curl_get($vv['url'] . "/clearCache");
        }
        return '';
    }

    /**
     * 删除指定资源
     * @param  int $id
     * @return array
     */
    public function delete($id)
    {

        try {
            $user = $this->getSessionUserInfo();
            $where = [
                "id" => $id,
                "node_id" => $user["node_id"]
            ];
            $data = $this->model->where($where)->find();
            if (!$this->model->where($where)->delete()) {
                Common::processException('删除失败');
            }
            $this->open_start('正在修改中');
            $type_id = $data['type_id'];
            $sitedata = $this->getQuestionSite($type_id);
            if (array_key_exists('status', $sitedata)) {
                return $sitedata;
            }
            foreach ($sitedata as $kk => $vv) {
//                $send = [
//                    "id" => $id,
//                    "type_id" => $type_id,
//                    "searchType" => 'question',
//                ];
//                $this->curl_post($vv['url'] . "/index.php/removeHtml", $send);
                $this->curl_get($vv['url'] . "/clearCache");
            }
        } catch (ProcessException $e) {
            return $this->resultArray('failed', $e->getMessage());
        }
    }


    /**
     * 获取 问题对应的站点
     * @param $type_id
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws ProcessException
     */
    private function getQuestionSite($type_id)
    {
        //首先取出选择该文章分类的菜单 注意有可能是子菜单
        $where['flag'] = 2;
        $model_menu = (new Menu());
        $menu = $model_menu->where(function ($query) use ($where) {
            /** @var Model $query */
            $query->where($where);
        })->where(function ($query) use ($type_id) {
            /** @var Model $query */
            $query->where('type_id', ['=', $type_id], ['like', "%,$type_id,%"], 'or');
        })->select();
        if (!$menu) {
            Common::processException('问答分类没有菜单选中页面，暂时不能预览。');
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


    /***
     * 问答相关预览
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function questionShowHtml()
    {
        try {
            $data = $this->request->post();
            $sitedata = $this->getQuestionSite($data['type_id']);
            foreach ($sitedata as $kk => $vv) {
                $showhtml[] = [
                    'url' => $vv['url'] . '/question/question' . $data['id'] . '.html',
                    'site_name' => $vv['site_name'],
                ];
            }
            if (!empty($showhtml)) {
                return $this->resultArray($showhtml);
            } else {
                Common::processException('当前文章对应的菜单页面没有站点选择，暂时不能预览。');
            }
        } catch (ProcessException $e) {
            return $this->resultArray('failed', $e->getMessage());
        }

    }
}
