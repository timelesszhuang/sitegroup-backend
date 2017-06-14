<?php
/**
 * Created by PhpStorm.
 * User: qiangbi
 * Date: 17-6-14
 * Time: 上午8:58
 */
namespace app\admin\controller;
use think\Controller;

class CrontabTask extends Controller{

    public function index()
    {
        $sites=\app\admin\model\Site::where(1)->field("id,url")->select();
        foreach($sites as $item){
            yield $this->startTask($item->id);
        }
    }

    public function startTask($id)
    {
        echo 2222;

    }



}