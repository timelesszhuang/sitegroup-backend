<?php
/**
 * Created by PhpStorm.
 * User: jingzheng
 * Date: 2017/4/21
 * Time: 11:31
 */

namespace app\common\controller;
use think\Validate;

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

    /**
     * 添加公司
     * @return array
     * @auther guozhen
     */
    public function add()
    {
        if($this->request->isPost()){
            $rule=[
                ["name","require","请输入公司名称"],
                ["artificialperson","require","请输入法人"],
                ["manbusiness","require","请输入主营业务"],
                ["industry_id","require","请选择行业"],
                ["industry_name","require","请选择行业"]
            ];
            $validate=new Validate($rule);
            $data=$this->request->post();
            if(!$validate->check($data)){
                return $this->resultArray($validate->getError(),"failed");
            }
            if(!\app\common\model\Company::create($data)){
                return $this->resultArray("添加失败","failed");
            }
            return $this->resultArray("添加成功");
        }
    }

    /**
     * 修改数据
     * @return array
     * @auther jingzheng
     */
    public function update()
    {
        if ($this->request->isPut()) {
            $rule = [
                ["name","require","请输入公司名称"],
                ["artificialperson","require","请输入法人"],
                ["manbusiness","require","请输入主营业务"],
                ["industry_id","require","请选择行业"],
                ["industry_name","require","请选择行业"]
            ];
            $data = $this->request->put();
            $validate = new Validate($rule);
            if (!$validate->check($data)) {
                return $this->resultArray($validate->getError(), 'failed');
            }
            if (!\app\common\model\Company::update($data)) {
                return $this->resultArray('修改失败', 'failed');
            }
            return $this->resultArray();
        }
    }
    public function delete($id)
    {
        if ($this->request->isDelete()) {
            $Industry = \app\common\model\C::get($id);
            if (!$Industry->delete()) {
                return $this->resultArray('删除失败', 'failed');
            }
            return $this->resultArray('删除成功');

        }
    }





}



