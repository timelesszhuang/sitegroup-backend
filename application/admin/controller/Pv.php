<?php

namespace app\admin\controller;

use app\common\model\CountData;
use think\Controller;
use think\Request;
use think\Validate;
use app\common\controller\Common;

class Pv extends Common
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $limits = $this->getLimit();
        $site_id = $this->request->get('site_id');
        $where = [];
        if (!empty($site_id)) {
            $where['site_id'] = $site_id;
        }
        $user = $this->getSessionUser();
        $where["node_id"] = $user["user_node_id"];
        return $this->resultArray('', '', (new \app\admin\model\Pv())->getAll($limits['limit'], $limits['rows'], $where));
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
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {

    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {

    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
    }

    /**
     * 首页统计
     */
    public function countDatas()
    {
        $user = $this->getSessionUser();
        $ttime=strtotime(date("Y-m-d 00:00:00"));
        $cd=new CountData();
        return $this->resultArray('', '', [
            "pv"=>intval($cd->countPv($user["user_node_id"],$ttime)),
            "useragent"=>intval($cd->countUseragent($user["user_node_id"],$ttime)),
            "article"=>intval($cd->countArticle($user["user_node_id"],$ttime)),
            "shuaidan"=>intval($cd->countShuaidan($user["user_node_id"],$ttime)),
            "shoulu"=>intval($cd->countInclude($user["user_node_id"]))
        ]);
    }

}
