<?php

namespace app\common\controller;

use app\common\model\Site;
use app\common\model\SiteDetailPageinfo;
use app\common\model\SitePageinfo;
use think\Controller;
use think\Request;
use think\Session;
use think\Validate;
use app\common\controller\Common;
use app\common\traits\Obtrait;

class PageInfo extends CommonLogin
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
        $user_info = $this->getSessionUserInfo();
        $where = [];
        $where["node_id"] = $user_info["node_id"];
        $where["site_id"] = $user_info["site_id"];
        $data = (new SitePageinfo)->getAll($request["limit"], $request["rows"], $where);
        return $this->resultArray('', '', $data);
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
        $user_info = $this->getSessionUserInfo();
        $where = [];
        $where["node_id"] = $user_info["node_id"];
        $where["site_id"] = $user_info["site_id"];
        $data["site_name"] = $user_info["site_name"];
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

        $user_info = $this->getSessionUserInfo();
        $site_id = $user_info["site_id"];
        $wh['id'] = $site_id;
        $Site = new Site();
        $keyword_id = $Site->where($wh)->field('keyword_ids')->find()->keyword_ids;
//        dump($keyword_ids);die;
        $keyword_ids = explode(',', $keyword_id);
        $where['id'] = $keyword_ids;
        $keyword = new \app\common\model\Keyword();
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
        $request = $this->getLimit();
        $user_info = $this->getSessionUserInfo();
        $title= $this->request->get('title');
        $where = [];
        if (!empty($title)) {
            $where['title'] = ["like", "%$title%"];
        }
        $where["node_id"] = $user_info["node_id"];
        $where["site_id"] = $user_info["site_id"];
        $where['type'] = 'article';
        $data = (new SiteDetailPageinfo())->getAll($request["limit"], $request["rows"], $where);
        return $this->resultArray('', '', $data);
    }

    /**
     * @return array
     * 文章详情页获取当前的tdk
     */
    public function articletdksave($id)
    {
        return $this->getread((new SiteDetailPageinfo()), $id);

    }

    /**
     * 文章详情页修改当前的tdk
     */
    public function articletdkedit(Request $request, $id)
    {
        $rule = [
            ['title', 'require', "请填title"],
            ["keyword", "require", "请填keyword"],
            ["description", "require", "请填description"],
        ];
        $data = $this->request->post();
        $validate = new Validate($rule);
        if (!$validate->check($data)) {
            return $this->resultArray('failed',$validate->getError());
        }
        return $this->publicUpdate((new SiteDetailPageinfo()),$data,$id);


    }

    /**
     * @return array
     * 问答页tdk展示数据
     */
    public function questiontdk()
    {
        $request = $this->getLimit();
        $user_info = $this->getSessionUserInfo();
        $title= $this->request->get('title');
        $where = [];
        if (!empty($title)) {
            $where['title'] = ["like", "%$title%"];
        }
        $where["node_id"] = $user_info["node_id"];
        $where["site_id"] = $user_info["site_id"];
        $where['type'] = 'question';
        $data = (new SiteDetailPageinfo())->getAll($request["limit"], $request["rows"], $where);
        return $this->resultArray('', '', $data);
    }


    /**
     * @return array
     * 产品tdk展示数据
     */
    public function producttdk()
    {
        $request = $this->getLimit();
        $user_info = $this->getSessionUserInfo();
        $title= $this->request->get('title');
        $where = [];
        if (!empty($title)) {
            $where['title'] = ["like", "%$title%"];
        }
        $where["node_id"] = $user_info["node_id"];
        $where["site_id"] = $user_info["site_id"];
        $where['type'] = 'product';
        $data = (new SiteDetailPageinfo())->getAll($request["limit"], $request["rows"], $where);
        return $this->resultArray('', '', $data);

    }




}
