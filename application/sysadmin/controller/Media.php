<?php

namespace app\sysadmin\controller;

use app\common\controller\Common;
use think\Request;
use app\common\model\Media as Me;
use think\Validate;

class Media extends Common
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $request = $this->getLimit();
        $name = $this->request->get('name');
        $where = [];
        if (!empty($name)) {
            $where["name"] = ["like", "%$name%"];
        }
        $data = (new Me())->getAll($request["limit"], $request["rows"], $where);
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
            ["name", "require", "请输入媒体名称"],
            ["media_type_id", "require", "请输入媒体分类id"],
            ["media_type_name", "require", "请输入媒体分类名称"],
            ["origin", "require", "请输入地区名称"],
            ["origin_id", "require", "请输入地区名称id"],
            ['market_price', "require", "请输入市场价格"],
            ["assitant_price", "require", "请输入代理价格"],
            ["sale_price", "require", "请输入销售价格"]
        ];
        $validate = new Validate($rule);
        $post = $request->post();
        if (!$validate->check($post)) {
            return $this->resultArray($validate->getError(), 'failed');
        }
        if (!Me::create($post)) {
            return $this->resultArray("添加失败", "failed");
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
        return $this->getread((new Me()), $id);
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
            ["name", "require", "请输入媒体名称"],
            ["media_type_id", "require", "请输入媒体分类id"],
            ["media_type_name", "require", "请输入媒体分类名称"],
            ["origin", "require", "请输入地区名称"],
            ["origin_id", "require", "请输入地区名称id"],
            ['market_price', "require", "请输入市场价格"],
            ["assitant_price", "require", "请输入代理价格"],
            ["sale_price", "require", "请输入销售价格"]
        ];
        $validate = new Validate($rule);
        $put = $request->put();
        if (!(new Me())->save($put, ["id" => $id])) {
             return $this->resultArray("修改失败",'failed');
        }
        return $this->resultArray("修改成功");
    }

    /**
     * 删除指定资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        return $this->deleteRecord((new Me()),$id);
    }
}
