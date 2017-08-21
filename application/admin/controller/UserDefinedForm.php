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
                $field['field' . $i][] = $pdata['field' . $i];
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
        $data=(new UserForm)->where(["id" => $id])->field("create_time,update_time", true)->find();
        $data['form_info']=unserialize($data['form_info']);
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
                $field['field' . $i][] = $pdata['field' . $i];
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
        $tag = '';
        $form = <<<code
            <form action="/DefinedRejection" method="POST" name="userdefinedform" id="userdefinedform">
            <input type="hidden" name="tag" value="{$tag}"
                <div class="bannerBox_input">
                    <span class="name">姓名：</span>
                    <input name="name" id="userNameSales" class="input try_blur" value="" placeholder="请输入联系人姓名">
                    <span class="hint_phone" style="display:none"></span>
                    <a href="#" class="validate close" style="display:none"></a>
                </div>
                <div class="bannerBox_input">
                    <span class="name">电话：</span>
                    <input name="phone" type="text" class="input  try_blur " value="" placeholder="电话号码／手机号码">
                    <span class="hint_phone" style="display:none"></span>
                    <a href="#" class="validate close" style="display:none"></a>
                </div>
                <div class="bannerBox_input">
                    <span class="name">邮箱：</span>
                    <input name="email" id="email" class="input try_blur email" type="text" placeholder="请输入邮箱地址">
                    <span class="hint_phone" style="display:none"></span>
                    <a href="#" class="validate close" style="display:none"></a>
                </div>
                <div class="bannerBox_input">
                    <span class="name">公司：</span>
                    <input name="company" id="company" type="text" class="input try_blur" placeholder="请输入公司名称">
                    <!--隐藏表单-->
                    <input type="hidden" value="" name="ip">
                    <input type="hidden" value="input" name="query_string">
                    <!--这个参数是从搜索引擎中来-->
                    <input type="hidden" value="" name="key_word">
                    <!--搜索引擎-->
                    <input type="hidden" value="" name="search_engine">
                    <!--搜索引擎传递过来的地域信息-->
                    <input type="hidden" value="" name="s_val">
                    <!--位置信息 比如是qiangbi  还是胜途的区分-->
                    <input type="hidden" value="yizhixin" name="pos">
                    <!--表示是谁的客户 表示salesmen 中的 职员的id-->
                    <input type="hidden" value="0" name="s">
                    <span class="hint_phone" style="display:none"></span>
                    <a href="#" class="validate close" style="display:none"></a>
                </div>
                <input name="button" class="button" id="submit" value="提交申请" type="button">
            </form>

code;

    }

}
