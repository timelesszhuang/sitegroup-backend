<?php

namespace app\common\model;

use think\Model;

class HtmlTemplate extends Model
{
    /**
     * 初始化函数
     * @author guozhen
     */
    public static function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
        HtmlTemplate::event("before_write", function ($template) {
            if (!empty($template->path)) {
                $zip=new \ZipArchive();
                $root=dirname(THINK_PATH);
                $status='';
                if($zip->open($root."/public/".$template->path)==true){
                    $zip->extractTo($root.'/public/upload/unHtmlTemplate');
                    for($i = 0; $i < $zip->numFiles; $i++) {
                        $filename = $zip->getNameIndex($i);
                        $fileinfo = pathinfo($filename);
                        if(isset($fileinfo["dirname"])){
                            if($fileinfo["dirname"]!="." && strstr($fileinfo["dirname"],"/")===false){
                                $template->generated_path="unHtmlTemplate/".$fileinfo["dirname"]."/index.html";
                            }
                        }

                    }
                    $zip->close();
                }
            }
        });
    }

}
