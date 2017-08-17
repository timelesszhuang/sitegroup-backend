<?php

namespace app\user\controller;

use think\Controller;
use think\Request;
use app\admin\controller\Template as AdminTemplate;
class Template extends Controller
{
    /**
     * 根据site_id查询
     *
     * @return \think\Response
     */
    public function index($site_id)
    {
        return (new AdminTemplate)->filelist($site_id);
    }

    /**
     * 获取模板内容
     * @param $site_id
     * @param $name
     * @return \think\Response
     */
    public function read($site_id,$name)
    {
        return (new AdminTemplate)->templateRead($site_id,$name);
    }

    /**
     * 修改模板
     * @param $site_id
     * @param $name
     * @return \think\Response
     */
    public function save($site_id,$name)
    {
        return (new AdminTemplate)->save($site_id,$name);
    }

    /**
     * 添加模板
     * @param $site_id
     * @param $name
     * @return \think\Response
     */
    public function story($site_id,$name)
    {
        return (new AdminTemplate)->readFile($site_id,$name);
    }

}
