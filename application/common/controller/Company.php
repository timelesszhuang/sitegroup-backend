<?php
/**
 * Created by PhpStorm.
 * User: jingzheng
 * Date: 2017/4/21
 * Time: 11:31
 */

namespace app\common\controller;
class Company extends Common
{
    /**
     * 获取公司信息
     * @return array
     * @auther guozhen
     */
    public function index()
    {
        if($this->request->isGet()){
            $request=$this->getLimit();
            return (new \app\common\model\Company)->getCompany($request["limit"],$request["rows"]);
        }
    }

    public function add()
    {
        if($this->request->isPut()){
            $rule=[
                ["name","require","请输入公司名称"],
                ["artificialperson","require","请输入法人"],

            ];


        }


    }

}



