<?php

namespace app\admin\controller;

use think\Request;
use app\common\controller\Common;
use think\Validate;

class Question extends Common
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     * @author guozhen
     */
    public function index(Request $request)
    {
        $limits = $this->getLimit();
        $content = $request->get('content');
        $type_id=$request->get("type_id");
        $where = [];
        if (!empty($content)) {
            $where['question'] = ["like", "%$content%"];
        }
        if(!empty($type_id)){
            $where['type_id']=$type_id;
        }
        $user = (new Common)->getSessionUser();
        $where["node_id"] = $user["user_node_id"];
        return $this->resultArray('', '', (new \app\admin\model\Question)->getAll($limits['limit'], $limits['rows'], $where));
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
     * @param  \think\Request $request
     * @return \think\Response
     * @author guozhen
     */
    public function save(Request $request)
    {
        $rule = [
            ['question', "require", "请填写问题"],
            ['content_paragraph', 'require', "请填写答案"],
            ["type_id","require","请选择分类id"],
            ["type_name","require","请选择分类名称"]
        ];
        $validate = new Validate($rule);
        $data = $this->request->post();
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), 'failed');
        }
        $data["node_id"] = $this->getSessionUser()['user_node_id'];
        if (!\app\admin\model\Question::create($data)) {
            return $this->resultArray('添加失败', 'failed');
        }
        return $this->resultArray('添加成功');
    }

    /**
     * 显示指定的资源
     *
     * @param  int $id
     * @return \think\Response
     * @author guozhen
     */
    public function read($id)
    {
        return $this->getread((new \app\admin\model\Question),$id);
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
     * @author guozhen
     */
    public function update(Request $request, $id)
    {
        $rule = [
            ['question', "require", "请填写问题"],
            ['content_paragraph', 'require', "请填写答案"],
            ["type_id","require","请选择分类id"],
            ["type_name","require","请选择分类名称"]
        ];
        $validate = new Validate($rule);
        $data = $this->request->put();
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), 'failed');
        }
        return $this->publicUpdate((new \app\admin\model\Question),$data,$id);
    }

    /**
     * 删除指定资源
     *
     * @param  int $id
     * @return \think\Response
     * @author guozhen
     */
    public function delete($id)
    {
        return $this->deleteRecord((new \app\admin\model\Question),$id);
    }

}
