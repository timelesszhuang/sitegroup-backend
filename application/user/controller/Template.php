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

}
