<?php

namespace app\admin\controller;

use app\user\controller\PageInfo;
use app\user\model\SitePageinfo;
use think\Request;
use app\common\controller\Common;
use think\Validate;

class Tdk extends Common
{
    /**
     * 大后台统一修改站点tdk操作
     * @author guozhen
     * @param $id
     * @return \think\Response
     */
    public function save($id)
    {
        return (new PageInfo)->update($id);
    }

    /**
     * 查取对应site_id的网站
     * @param $id
     * @return array
     */
    public function search($id)
    {
        $request = $this->getLimit();
        $user = $this->getSessionUser();
        $where["node_id"] = $user["user_node_id"];
        $where["site_id"] = $id;
        $data = (new SitePageinfo)->getAll($request["limit"], $request["rows"], $where);
        return $this->resultArray('', '', $data);
    }

    /**
     * 获取单条记录
     * @param $id
     * @return array
     */
    public function read($id)
    {
        return $this->getread((new SitePageinfo), $id);
    }

    /**
     * @param $id
     * @return array
     * 获取A关键词
     */
    public function getAkeyword($id)
    {
        $wh['id'] = $id;
        $Site = new \app\admin\model\Site();
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


}
