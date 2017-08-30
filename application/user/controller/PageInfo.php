<?php

namespace app\user\controller;

use app\admin\model\Site;
use app\user\model\SitePageinfo;
use think\Controller;
use think\Request;
use think\Validate;
use app\common\controller\Common;
use app\common\traits\Obtrait;

class PageInfo extends Common
{
    use  Obtrait;

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
        $data = (new SitePageinfo)->getAll($request["limit"], $request["rows"], $where);
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
            ["page_id", "require", "请输入页面id"],
        ];
        $validate = new Validate($rule);
        $data = $request->post();
        $data['node_id'] = $this->getSiteSession('login_site')["node_id"];
        $data["site_id"] = $this->getSiteSession('website')["id"];
        $data["site_name"] = $this->getSiteSession('website')["site_name"];
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), "failed");
        }
        if (!SitePageinfo::create($data)) {
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
        return $this->getread((new SitePageinfo), $id);
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
    public function update($id)
    {
        $rule = [
            ["page_id", "require", "请输入页面id"],
        ];
        $validate = new Validate($rule);
        $request = Request::instance();
        $data = $request->put();
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), "failed");
        }
        return $this->publicUpdate((new SitePageinfo), $data, $id);
    }

    /**
     * 删除指定资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        return $this->deleteRecord((new SitePageinfo), $id);
    }

    /**
     * @param $id
     * @return array
     * 获取A关键词
     */
    public function getAkeyword()
    {

        $site_id = $this->getSiteSession('website')["id"];
        $wh['id'] = $site_id;
        $Site = new Site();
        $keyword_id = $Site->where($wh)->field('keyword_ids')->find()->keyword_ids;
//        dump($keyword_ids);die;
        $keyword_ids = explode(',', $keyword_id);
        $where['id'] = $keyword_ids;
        $keyword = new \app\admin\model\Keyword();
        $data = $keyword->where('id', 'in', $keyword_ids)->field('id,name as text')->select();
        return $this->resultArray('', '', $data);
    }

    /**
     * @return array
     * 修改tdk中的akeyword_id
     */
    public function editpageinfo()
    {
        $data = $this->request->post();
        if ($data['akeyword_id'] == 0) {
            return $this->resultArray('首页关键词不能修改', 'failed');
        }
        if (!SitePageinfo::update($data)) {
            return $this->resultArray('修改失败', 'failed');
        }
        return $this->resultArray('修改成功');
    }

    /**
     * @return array
     * 文章详情页tdk修改获取显示数据
     */
    public function articletdk()
    {
        $page = $this->request->get('page');
        if (empty($page)) {
            $page = 1;
        }
        $site_id = $this->getSiteSession('website')["id"];
        $where['id'] = $site_id;
        $site = (new Site())->where($where)->select();
        foreach ($site as $k => $v) {
            $sitedata = $this->curl_get($v['url'] . "/index.php/showStaticList/article/" . $page);
            $data = json_decode($sitedata, true);
        }
//        dump($data);die;
        return $this->resultArray($data["msg"], '', $data["data"]);


    }

    /**
     * @return array
     * 文章详情页获取当前的tdk
     */
    public function articletdksave()
    {
        $edit = $this->request->get('edit');
        $et = strstr($edit, '.', TRUE);
        $site_id = $this->getSiteSession('website')["id"];
        $where['id'] = $site_id;
        $site = (new Site())->where($where)->select();
        foreach ($site as $k => $v) {
            $sitedata = $this->curl_get($v['url'] . "/index.php/getStaticOne/article/" . $et);
            $data = json_decode($sitedata, true);
        }
//        dump($v['url'] . "/index.php/etStaticOne/article/" . $et);
//        die;
        return $this->resultArray($data["msg"], '', $data["data"]);

    }

    /**
     * 文章详情页修改当前的tdk
     */

    public function articletdkedit()
    {
        $this->open_start('正在修改中');
        $content = $this->request->post('content');
        $filename = $this->request->post('filename');
        $site_id = $this->getSiteSession('website')["id"];
        $where['id'] = $site_id;
        $site = (new Site())->where($where)->select();
        foreach ($site as $k => $v) {
            $send = [
                "content" => $content
            ];
            $this->curl_post($v['url'] . "/index.php/generateOne/article/".$filename ,$send);
        }
    }


}
