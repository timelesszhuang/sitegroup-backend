<?php

namespace app\admin\controller;

use app\common\controller\Common;
use think\Validate;
use think\Request;

class Articletype extends Common
{
    /**
     * @return array
     */
    public function index()
    {
        $name = $this->request->get('name');
        $id = $this->request->get('id');

        $data = (new \app\admin\model\Articletype())->getArticletype($name, $id);
        return $this->resultArray('', '', $data);
    }

    /**
     * @param $id
     * @return array
     */
    public function read($id)
    {
        return $this->resultArray('','',\app\admin\model\Articletype::get($id));
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
            ["name", "require", "请输入文章名"],
            ["detail", "require", "请输入详情"],
            ["alias", "require", "请输入别名"],
        ];
        $validate = new Validate($rule);
        $data = $this->request->post();
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), "failed");
        }
        if (!\app\admin\model\Articletype::create($data)) {
            return $this->resultArray("添加失败", "failed");
        }
        return $this->resultArray("添加成功");
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
     * @param  int $id
     * @return \think\Response
     */
    public function delete($id)
    {

    }


}
