<?php

namespace app\common\controller;

use app\common\controller\Common;
use think\Request;
use app\common\model\ArticleReplaceKeyword as AreplaceKeyword;
use think\Validate;

class ArticleReplaceKeyword extends CommonLogin
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $request = $this->getLimit();
        $keyword = $this->request->get('keyword');
        $where = [];
        if (!empty($keyword)) {
            $where["keyword"] = ["like", "%$keyword%"];
        }
        $user_info = $this->getSessionUserInfo();
        $where["node_id"] =$user_info["node_id"];
        if ($user_info['user_type_name'] == 'site' && $user_info['user_type'] == '3') {
            $where["site_id"] = $user_info["site_id"];
        }
        $data = (new AreplaceKeyword())->getAll($request["limit"], $request["rows"], $where);
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
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $rule = [
            ["keyword", "require", "请输入关键词"],
            ["title", "require", "请输入title"],
            ["link", "require", "请输入链接"],
        ];
        $validate = new Validate($rule);
        $data = $request->post();
        if (!$validate->check($data)) {
            return $this->resultArray("failed",$validate->getError());
        }
        $user_info = $this->getSessionUserInfo();
        $data['node_id'] = $user_info["node_id"];
        $data["site_id"] = $user_info["site_id"];
        $data['replaceLink']='<a href="'.$data['link'].'" target="_blank" title="'.$data['title'].'">'.$data['keyword'].'</a>';
        if (!AreplaceKeyword::create($data)) {
            return $this->resultArray( "failed","添加失败");
        }
        return $this->resultArray("添加成功");
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        return $this->getread((new AreplaceKeyword), $id);
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
        $rule = [
            ["keyword", "require", "请输入关键词"],
            ["title", "require", "请输入title"],
            ["link", "require", "请输入链接"],
        ];
        $validate = new Validate($rule);
        $data = $request->post();
        if (!$validate->check($data)) {
            return $this->resultArray( "failed",$validate->getError());
        }
        $data['replaceLink']='<a href="'.$data['link'].'" target="_blank" title="'.$data['title'].'">'.$data['keyword'].'</a>';
        if (!(new AreplaceKeyword)->save($data, ["id" => $id])) {
            return $this->resultArray( 'failed','修改失败');
        }
        return $this->resultArray('修改成功');
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        return $this->deleteRecord((new AreplaceKeyword),$id);
    }
}
