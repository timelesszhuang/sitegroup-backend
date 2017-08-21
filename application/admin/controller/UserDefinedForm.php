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
     * @param  \think\Request $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $rule = [
            ['detail', 'require', '请填写描述'],
        ];
        $validate = new Validate($rule);
        $pdata = $request->post();
        if (!$validate->check($pdata)) {
            return $this->resultArray($validate->getError(), 'faile');
        }
        $data = [];
        $field = [];
        for ($i = 1; $i <= 4; $i++) {
            if (isset($pdata['field' . $i]) && isset($pdata['field' . $i]['name']) && !empty($pdata['field' . $i]['name'])) {
                $field['field' . $i][] = $pdata['field' . $i];
            }
        }
        $data['detail'] = $pdata['detail'];
        $data['from_info'] = $field;
        $data['tag'] = md5(uniqid(rand(), true));
        if (!UserForm::create($data)) {
            return $this->resultArray('添加失败', 'faile');
        }
        return $this->resultArray('添加成功');
    }

    /**
     * 显示指定的资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request $request
     * @param  int $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }

    /**
     * 获取 js html 代码
     * @param int $id
     */
    public function getFormCode($id)
    {
        //唯一id
        print_r($id);
        $tag = '';
        $defined_info = UserForm::get($id);
        print_r($defined_info->tag);
        $form_info = unserialize($defined_info->form_info);
        print_r($form_info);
        foreach ($form_info as $k => $v) {

        }
        $form = <<<code
            <form action="/DefinedRejection" method="POST" name="userdefinedform" id="userdefinedform">
                <input type="hidden" name="tag" value="{$tag}"
                <div class="bannerBox_input">
                    <span class="name">：</span>
                    <input name="name" id="userNameSales" class="input" value="" placeholder="">
                </div>
                <div class="bannerBox_input">
                    <span class="name">：</span>
                    <input name="phone" type="text" class="input " value="" placeholder="">
                </div>
                <div class="bannerBox_input">
                    <span class="name">：</span>
                    <input name="email" id="email" class="input" type="text" placeholder="">
                </div>
                <div class="bannerBox_input">
                    <span class="name">：</span>
                    <input name="company" id="company" type="text" class="input try_blur" placeholder="">
                </div>
                <input name="button" class="button" id="submit" value="提交数据" type="button">
            </form>
code;

    }

}
