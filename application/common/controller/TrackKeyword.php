<?php

namespace app\common\controller;


use think\Request;
use think\Validate;


class TrackKeyword extends CommonLogin
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $limits = $this->getLimit();
        $keyword = $this->request->get('keyword');
        $where = [];
        if (!empty($keyword)) {
            $where['keyword'] = ["like", "%$keyword%"];
        }
        $user_info = $this->getSessionUserInfo();
        $where["node_id"] =$user_info["node_id"];
        return $this->resultArray('', '', (new \app\common\model\TrackKeyword())->getAll($limits['limit'], $limits['rows'], $where));
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
            ['keyword', 'require', "请填写追踪关键词"],
        ];
        $validate = new Validate($rule);
        $data = $this->request->post();
        if (!$validate->check($data)) {
            return $this->resultArray( 'failed',$validate->getError());
        }
        $data["node_id"] = $this->getSessionUser()['user_node_id'];
        $track =  new \app\common\model\TrackKeyword();
        $where['node_id'] = $data["node_id"];
        $count =  $track->where($where)->count();
        if($count>=4){
            return $this->resultArray('failed','添加失败,此节点最多添加四个');
        }
        if (!\app\common\model\TrackKeyword::create($data)) {
            return $this->resultArray( 'failed','添加失败');
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
        return $this->getread((new \app\common\model\TrackKeyword), $id);
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
            ['keyword', 'require', "请填写追踪关键词"],
        ];
        $validate = new Validate($rule);
        $data = $this->request->put();
        if (!$validate->check($data)) {
            return $this->resultArray('failed',$validate->getError());
        }
        return $this->publicUpdate((new \app\common\model\TrackKeyword()),$data,$id);
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        return $this->deleteRecord((new \app\common\model\TrackKeyword),$id);
    }

    /**
     * 获取所有code
     * @return array
     */
    public function getTrack()
    {
        $field="id,keyword as text";
        return $this->getList((new \app\common\model\TrackKeyword()),$field);
    }
}
