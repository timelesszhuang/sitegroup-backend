<?php
/**
 * Created by PhpStorm.
 * User: qiangbi
 * Date: 17-4-26
 * Time: 下午2:25
 */

namespace app\wx\model;

use app\common\traits\Osstrait;
use think\Config;
use think\Model;

class WxSmallApp extends Model
{
    use Osstrait;

    public function getAppId($node_id){
        $app_info = $this->where(['node_id'=>$node_id])->find();
        if($app_info){
            $app_id =  $app_info['id'];
        }else{
            $this->add(['node_id'=>$node_id]);
            $app_id = $this->getLastId();
        }
        return $app_id;
    }

}