<?php

namespace app\user\controller;

use app\common\model\BrowseRecord;
use think\Request;
use app\common\controller\Common;
use think\Session;
use think\Validate;
class Acount extends Common
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
//      $request=$this->getLimit();
//      $node_id=$this->getSiteSession('login_site');
        $start_time=$this->request->get('time');

        $time = strtotime($start_time);
        dump($time);die;
        $where = [
            'create_time'=>'between',$time,
            'node_id'=>2,
            'site_id'=>1
        ];
        $arr = (new BrowseRecord())->field('engine,count(id) as keyCount')->where($where)->group('engine')->select();
        $arrcount = (new BrowseRecord())->where($where)->count();
        $temp=[];

        foreach ($arr as $k=>$v){
            $temp[]=[$v['engine'],round($v['keyCount']/$arrcount*100,2)];
        }
        return $this->resultArray('','',$temp);

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
     * 小网站用户存储站点信息
     * @return array
     */
    public function siteInfo()
    {

    }

    /**
     * 设置session 全部都放进去 以后有用
     * @param $site_id
     * @param $site_name
     */
    public function setSession($site_info)
    {

    }
}
