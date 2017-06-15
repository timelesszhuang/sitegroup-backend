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

    /**
     * 执行定时一键更新网站
     */
    public function index()
    {
        foreach($this->startTask() as $item){
            pclose(popen("curl $item &","r"));
        }
    }

    /**
     * 循环任务 获取站点信息
     * @return \Generator
     */
    public function startTask()
    {
        $sites=\app\admin\model\Site::where(1)->field("url")->select();
        foreach($sites as $item){
            yield $this->getUrl($item);
        }

    }

    /**
     * 获取站点的url
     * @param $item
     * @return string
     */
    public function getUrl($item)
    {
        return $item->url."/allstatic";
    }


}