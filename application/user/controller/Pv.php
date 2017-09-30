<?php

namespace app\user\controller;

use app\common\controller\Common;
use app\user\model\CountData;
use think\Request;

class Pv extends Common
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
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
        //
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
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
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }

    /**
     * 首页统计
     */
    public function countDatas()
    {
        $siteinfo = Site::getSiteInfo();
        $ttime=strtotime(date("Y-m-d 00:00:00"));
        $cd=new CountData();
        return $this->resultArray('', '', [
            "pv"=>intval($cd->countPv($siteinfo,$ttime)),
            "useragent"=>intval($cd->countUseragent($siteinfo,$ttime)),
            "article"=>intval($cd->countArticle($siteinfo,$ttime)),
            "shuaidan"=>intval($cd->countShuaidan($siteinfo,$ttime)),
            "shoulu"=>intval($cd->countInclude($siteinfo))
        ]);
    }
}
