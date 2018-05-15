<?php

namespace app\common\controller;


use think\Request;
use think\Validate;
use app\common\model\UserDefinedForm as UserForm;

class UserDefinedForm extends CommonLogin
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
        $user_info = $this->getSessionUserInfo();
        $where["node_id"] = $user_info["node_id"];
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
            return $this->resultArray('failed', $validate->getError());
        }
        $data = [];
        $field = [];
        for ($i = 1; $i <= 4; $i++) {
            if (isset($pdata['field' . $i]) && isset($pdata['field' . $i]['name']) && !empty($pdata['field' . $i]['name'])) {
                $pdata['field' . $i]['require'] = (boolean)$pdata['field' . $i]['require'];
                $field['field' . $i] = $pdata['field' . $i];
            }
        }
        $data['detail'] = $pdata['detail'];
        $data['form_info'] = serialize($field);
        $data['tag'] = md5(uniqid(rand(), true));
        $user_info = $this->getSessionUserInfo();
        $data["node_id"] = $user_info["node_id"];
        if (!UserForm::create($data)) {
            return $this->resultArray('failed', '添加失败');
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
            return $this->resultArray('failed', $validate->getError());
        }
        $data = [];
        $field = [];
        for ($i = 1; $i <= 4; $i++) {
            if (isset($pdata['form_info']['field' . $i]) && isset($pdata['form_info']['field' . $i]['name']) && !empty($pdata['form_info']['field' . $i]['name'])) {
                $pdata['form_info']['field' . $i]['require'] = (boolean)$pdata['form_info']['field' . $i]['require'];
                $field['field' . $i] = $pdata['form_info']['field' . $i];
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
            return $this->resultArray('failed', '该条记录不存在，请查证', []);
        }
        $tag = $defined_info->tag;
        $form_info = unserialize($defined_info->form_info);
        $field5 = [
            'code' => [
                'name' => '验证码',
                'type' => 'text',
                'placeholder' => '请输入验证码',
                'require' => true,
            ]
        ];
        $form_info1 = array_merge($form_info, $field5);
        $form_field = '';
        $js_field = '';
        foreach ($form_info1 as $k => $v) {
            $type = $v['type'];
            $name = $v['name'];
            $placeholder = $v['placeholder'];
            $require = $v['require'];
            $require_tag = '*';
            if (!$require) {
                $require_tag = '';
            }
            $per_field = '';
            if ($type == 'text') {
                $per_field = <<<code
                <div>
                    <span class="name">{$name}：</span>
                    <input name="{$k}" id="{$k}" type="text" placeholder="{$placeholder}">
                    <span>{$require_tag}</span>
                </div>
code;
            } else {
                $per_field = <<<code
                <div>
                    <span class="name">{$name}：</span>
                    <textarea name="{$k}" id="{$k}" type="text" placeholder="{$placeholder}"></textarea>
                    <span>{$require_tag}</span>
                </div>
code;
            }
            if ($require) {
                $js_code = <<<code
                if(!$('#{$k}').val()){
                         alert('{$name}为必填字段');
                         return false;
                }
code;
                $js_field .= $js_code;
            }
            $form_field .= $per_field;
        }
        $form = <<<code
            <script src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.js"></script>
            <!--请首先判断 页面中是否已经引用了 jquery-->
            <form action="" method="POST" name="userdefinedform" id="userdefinedform" >
                <input type="hidden" name="tag" value="{$tag}">
                {$form_field}
                <input name="submit_bottom" class="submit_bottom" id="submit_bottom" value="提交数据" type="button">
            </form>
            <script>
            $('#submit_bottom').click(function () {
                {$js_field}
                var data = $('#userdefinedform').serialize();
                var url = '/DefinedRejection';
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    async: true,
                    url: url,
                    data: data,
                    success: function (data) {
                        alert(data.msg);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        alert('尊敬的用户，服务暂时不可用，请稍后再试或联系我们');
                    }
                });
            });
            </script>
code;
        return $this->resultArray('success', '获取自定义表单代码成功', $form);
    }

}
