<?php

namespace app\admin\controller;

use app\common\controller\Common;
use think\Request;
use think\Validate;
use app\admin\model\UserDefinedForm as UserForm;
class UserDefinedForm extends Common
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {

    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
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
     */
    public function save(Request $request)
    {
        $rule=[
            ['detail','require','请填写描述'],
        ];
        $validate=new Validate($rule);
        $pdata=$request->post();
        if(!$validate->check($pdata)){
             return $this->resultArray($validate->getError(),'faile');
        }
        $data=[];
        $field=[];
        for ($i=1;$i<=4;$i++){
            if(isset($pdata['field'.$i]) && isset($pdata['field'.$i]['name']) && !empty($pdata['field'.$i]['name'])){
                $field['field'.$i][]=$pdata['field'.$i];
            }
        }
        $data['detail']=$pdata['detail'];
        $data['from_info']=$field;
        $data['tag']=md5 ( uniqid ( rand (), true ));
        if (!UserForm::create($data)) {
            return $this->resultArray('添加失败', 'faile');
        }
        return $this->resultArray('添加成功');
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
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
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}
