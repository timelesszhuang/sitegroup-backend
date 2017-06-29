<?php

namespace app\admin\controller;

use app\common\traits\Obtrait;
use think\Controller;
use think\Request;
use app\common\controller\Common;
class MobileTemplate extends Common
{
    use Obtrait;
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index($site_id)
    {
        $url="templatelist";
        $site=\app\admin\model\Site::get($site_id);
        if($site){
            $siteData=$this->curl_get($site->url."/index.php/$url?site_id=".$site_id);
            $data=json_decode($siteData,true);
            return $this->resultArray($data['msg'],'',$data["filelist"]);
        }
        return $this->resultArray('当前网站未获取到!','failed');
    }


    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function templateRead($site_id,$name)
    {
        $url="templateread";
        $site=\app\admin\model\Site::get($site_id);
        if($site){
            $siteData=$this->curl_get($site->url."/index.php/$url?site_id=".$site_id."&filename=".$name);
            $data=json_decode($siteData,true);
            return $this->resultArray($data['msg'],'',["content"=>$data["content"],"filename"=>$data["filename"]]);
        }
        return $this->resultArray('当前网站未获取到!','failed');
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save($site_id,$name)
    {
        $request=Request::instance();
        $content=$request->post("content");
        $url="templateupdate";
        $site=\app\admin\model\Site::get($site_id);
        if($site) {
            $send = [
                "site_id" => $site_id,
                "filename" => $name,
                "content" => $content
            ];
            $siteData=$this->curl_post($site->url."/index.php/".$url,$send);
            $data=json_decode($siteData,true);
            return $this->resultArray($data['msg'],$data["status"]);
        }
        return $this->resultArray('当前网站未获取到!','failed');
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($site_id,$name)
    {
        $request=Request::instance();
        $content=$request->post("content");
        $url="templateadd";
        $site=\app\admin\model\Site::get($site_id);
        if($site) {
            $send = [
                "site_id" => $site_id,
                "filename" => $name,
                "content" => $content
            ];
            $siteData=$this->curl_post($site->url."/index.php/".$url,$send);
            $data=json_decode($siteData,true);
            return $this->resultArray($data['msg'],$data["status"]);
        }
        return $this->resultArray('当前网站未获取到!','failed');
    }

}
