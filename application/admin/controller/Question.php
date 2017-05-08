<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\common\controller\Common;
use think\Validate;

class Question extends Common
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     * @auther guozhen
     */
    public function index(Request $request)
    {
        $limits=$this->getLimit();
        $content=$request->get('content');
        $where=[];
        if(!empty($content)){
            $where['question']=["like","%$content%"];
        }
        $user=(new Common)->getSessionUser();
        $where["node_id"]=$user["user_node_id"];
        return $this->resultArray('','',(new \app\admin\model\Question)->getAll($limits['limit'],$limits['rows'],$where));
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     *
     */
    public function create()
    {
        //
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     * @auther guozhen
     */
    public function save(Request $request)
    {
        $rule=[
            ['question',"require","请填写问题"],
            ['content_paragraph','require',"请填写答案"]
        ];
        $validate=new Validate($rule);
        $data=$this->request->post();
        if(!$validate->check($data)){
            return $this->resultArray($validate->getError(),'faile');
        }
        $data["node_id"]=$this->getSessionUser()['user_node_id'];
        if(!\app\admin\model\Question::create($data)){
            return $this->resultArray('添加失败','faile');
        }
        return $this->resultArray('添加成功');
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     * @auther guozhen
     */
    public function read($id)
    {
        return $this->resultArray('','',\app\admin\model\Question::where(["id"=>$id])->field('id,question,create_time,content_paragraph')->find());
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     * @auther guozhen
     */
    public function update(Request $request, $id)
    {
        $rule=[
            ['question',"require","请填写问题"],
            ['content_paragraph','require',"请填写答案"]
        ];
        $validate=new Validate($rule);
        $data=$this->request->put();
        if(!$validate->check($data)){
            return $this->resultArray($validate->getError(),'faile');
        }
        $data["node_id"]=$this->getSessionUser()['user_node_id'];
        if(!\app\admin\model\Question::create($data)){
            return $this->resultArray('修改失败','faile');
        }
        return $this->resultArray('修改成功');
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     * @auther guozhen
     */
    public function delete($id)
    {
        if(!\app\admin\model\Question::destroy($id)){
            return $this->resultArray('删除失败','faile');
        }
        return $this->resultArray('删除成功');
    }
}
