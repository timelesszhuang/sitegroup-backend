<?php

namespace app\common\controller;


use think\Request;
use app\common\controller\Common;
use think\Session;
use think\Validate;

class ArticleInsertA extends Common
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $href = $this->request->get('href');
        $request = $this->getLimit();
        $user_info = $this->getSessionUserInfo();
        $where["node_id"] =$user_info["node_id"];
        if ($user_info['user_type_name'] == 'site' && $user_info['user_type'] == '3') {
            $where["site_id"] = $user_info["site_id"];
        }
        if (!empty($href)) {
            $where["href"] = ["like", "%$href%"];
        }
        $data = (new \app\common\model\ArticleInsertA())->getAll($request["limit"], $request["rows"], $where);
        return $this->resultArray($data);
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
            ["title", "require", "请输入title"],
            ["content", "require", "请输入内容"],
            ["href", "require", "请输入链接"],
        ];
        $validate = new Validate($rule);
        $data = $request->post();
        $url = strstr($data['href'], "http://");
        if (empty($url)) {
            return $this->resultArray( "failed",'请输入http://');
        }
        $user_info = $this->getSessionUserInfo();
        $data['node_id'] = $user_info["node_id"];
        $data["site_id"] = $user_info["site_id"];
        if (!$validate->check($data)) {
            return $this->resultArray("failed",$validate->getError() );
        }
        if (!\app\common\model\ArticleInsertA::create($data)) {
            return $this->resultArray( "failed","添加失败");
        }
        return $this->resultArray("添加成功");
    }

    /**
     * 显示指定的资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function read($id)
    {
        return $this->getread((new \app\common\model\ArticleInsertA()), $id);
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
        $rule = [
            ["title", "require", "请输入title"],
            ["content", "require", "请输入内容"],
            ["href", "require", "请输入链接"],
        ];
        $validate = new Validate($rule);
        $data = $request->post();
        $user_info = $this->getSessionUserInfo();
        $data['node_id'] = $user_info["node_id"];
        $data["site_id"] = $user_info["site_id"];
        if (!$validate->check($data)) {
            return $this->resultArray("failed",$validate->getError());
        }
        if (!(new \app\common\model\ArticleInsertA())->save($data, ["id" => $id])) {
            return $this->resultArray( 'failed','修改失败');
        }
        return $this->resultArray('修改成功');
    }

    /**
     * 删除指定资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        return $this->deleteRecord((new \app\common\model\ArticleInsertA()), $id);
    }

}
