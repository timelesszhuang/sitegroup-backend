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
        $request = $this->getLimit();
        $name = $this->request->get('detail');
        $where = [];
        if (!empty($detail)) {
            $where["detail"] = ["like", "%$detail%"];
        }
        $user = $this->getSessionUser();
        $where["node_id"] = $user["user_node_id"];
        $data = (new UserForm())->getAll($request["limit"], $request["rows"], $where);

        return $this->resultArray('', '', $data);
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
                $pdata['field' . $i]['require']=(boolean)$pdata['field' . $i]['require'];
                $field['field' . $i] = $pdata['field' . $i];
            }
        }
        $data['detail'] = $pdata['detail'];
        $data['form_info'] = serialize($field);
        $data['tag'] = md5(uniqid(rand(), true));
        $user = $this->getSessionUser();
        $data["node_id"] = $user["user_node_id"];
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
        $data = (new UserForm)->where(["id" => $id])->field("create_time,update_time", true)->find();
        $data['form_info'] = unserialize($data['form_info']);
        return $this->resultArray('', '', $data);
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int $id
     * @return \think\Response
     */
    public function edit($id)
    {

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
                $pdata['field' . $i]['require']=(boolean)$pdata['field' . $i]['require'];
                $field['field' . $i] = $pdata['field' . $i];
            }
        }
        $data['detail'] = $pdata['detail'];
        $data['form_info'] = serialize($field);
        return $this->publicUpdate((new UserForm), $data, $id);
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
        $defined_info = UserForm::get($id);
        if (!$defined_info) {
            return $this->resultArray('该条记录不存在，请查证', 'failed', []);
        }
        $tag = $defined_info->tag;
        $form_info = unserialize($defined_info->form_info);
        $form_field = '';
        foreach ($form_info as $k => $v) {
            $type = $v['type'];
            $name = $v['name'];
            $placeholder = $v['placeholder'];
            if ($type == 'text') {
                $per_field = <<<code
                <div>
                    <span class="name">{$name}：</span>
                    <input name="{$k}" id="{$k}" type="text" placeholder="{$placeholder}">
                </div>
code;
            } else {
                $per_field = <<<code
                <div>
                    <span class="name">{$name}：</span>
                    <textarea name="{$k}" id="{$k}" type="text" placeholder="{$placeholder}"></textarea>
                </div>
code;
            }
            $form_field .= $per_field;
        }
        $form = <<<code
            <form action="/DefinedRejection" method="POST" name="userdefinedform" id="userdefinedform">
                <input type="hidden" name="tag" value="{$tag}">
                {$form_field}
                <input name="button" class="button" id="submit" value="提交数据" type="button">
            </form>
code;
        $this->resultArray('获取自定义表单代码成功', '', $form);
    }

}
