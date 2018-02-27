<?php

namespace app\admin\controller;

use app\common\controller\PageInfo;
use app\common\model\SitePageinfo;
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
        $rule = [
            ["page_id", "require", "请输入页面id"],
        ];
        $validate = new Validate($rule);
        $request = Request::instance();
        $data = $request->put();
        if (!$validate->check($data)) {
            return $this->resultArray("failed",$validate->getError() );
        }
        return $this->publicUpdate((new SitePageinfo), $data, $id);

    }

    /**
     * 查取对应site_id的网站
     * @param $id
     * @return array
     */
    public function search($id)
    {
        $request = $this->getLimit();
        $user_info = $this->getSessionUserInfo();
        $where["node_id"] = $user_info["node_id"];
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
        $Site = new \app\common\model\Site();
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
            return $this->resultArray( 'failed','首页关键词不能修改');
        }
        if (SitePageinfo::update($data)) {
            return $this->resultArray('修改成功');
        }else{
            return $this->resultArray('failed','修改失败');
        }
        return $this->resultArray('修改成功');
    }


}
