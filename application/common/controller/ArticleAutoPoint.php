<?php

namespace app\common\controller;


use think\Request;
use think\Validate;

class ArticleAutoPoint extends CommonLogin
{
    /**
     * 显示资源列表
     */
    public function index()
    {
        $type_name = $this->request->get('type');
        $user_info = $this->getSessionUserInfo();
        $where["node_id"] = $user_info["node_id"];
        $where["type_name"] = $type_name;
        $return = (new \app\common\model\ArticleAutoPoint())->field("type_name")->where($where)->select();
        return $this->resultArray($return);
    }

    /**
     * 保存新建的资源
     * @param $name
     * @param $type
     * @return bool
     */
    public function save($name, $type)
    {
        $ArticleAutoPoint = (new \app\common\model\ArticleAutoPoint());
        $data['head']=$name;
        $user_info = $this->getSessionUserInfo();
        $data['node_id']=$user_info["node_id"];
        $data['type_name']=$type;
        if(!$ArticleAutoPoint->where($data)->find()){
            if($ArticleAutoPoint->create($data)){
                return true;
            }else{
                return false;
            }
        }else{
            return true;
        }
    }
}
