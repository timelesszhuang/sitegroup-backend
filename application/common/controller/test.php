<?php
/**
 * Created by PhpStorm.
 * User: qiangbi
 * Date: 18-3-14
 * Time: 上午11:37
 */

namespace app\common\controller;
use think\Request;

class test
{
    public function test(Request $request){
        $data = $request->param();
        if($data['name']=='jingyang'){
            return "这是帅哥";
        }else{
            return '这是丑逼';
        }
    }
}