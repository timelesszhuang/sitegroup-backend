<?php

namespace app\common\controller;

use think\Controller;

class Omapi extends Controller
{
    /**
     * 显示资源列表
     * @return \think\Response
     */
    public function index()
    {
        //
        $xmldata = file_get_contents('php://input');
        file_put_contents('a.txt', 'DSADAS', FILE_APPEND);
        file_put_contents('a.txt', $xmldata, FILE_APPEND);
    }
}
