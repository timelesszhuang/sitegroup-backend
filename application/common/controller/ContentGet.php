<?php

namespace app\common\controller;

use app\common\traits\Osstrait;
use think\Validate;
use think\Request;
use app\common\traits\Obtrait;
use app\common\model\ContentGet as Content_get;

class ContentGet extends CommonLogin
{
    use Obtrait;
    use Osstrait;

    /**
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index(Request $request)
    {
        $request = $this->getLimit();
        $name = $this->request->get('name');
        if($name){
            $where["name"] = ["like", "%$name%"];
        }
        $user_info = $this->getSessionUserInfo();
        $where["node_id"] = $user_info["node_id"];
        $data = (new Content_get)->getContentList($request["limit"], $request["rows"], $where);
        return $this->resultArray('', '', $data);
    }

    /**
     * @param $id
     * @return array
     */
    public function read($id)
    {
        return $this->getread((new Content_get), $id);
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request $request
     * @return array
     */
    public function save(Request $request)
    {
        $rule = [
            ["name", "require", "请输入名称"],
            ["en_name", "require|alphaNum|unique:imglist,en_name^node_id", "请输入英文名称|英文名格式只支持字母与数字|英文名重复"],
            ["href", "url", "链接格式不正确"],
        ];
        $validate = new Validate($rule);
        $data = $request->post();
        $user_info = $this->getSessionUserInfo();
        $data['node_id'] =  $user_info["node_id"];
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), "failed");
        }
        if (!Content_get::create($data)) {
            return $this->resultArray("添加失败", "failed");
        }
        return $this->resultArray("添加成功");
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request $request
     * @param  int $id
     * @return array
     */
    public function update(Request $request, $id)
    {
        $rule = [
            ["name", "require", "请输入名称"],
            ["en_name", "require|alphaNum|unique:imglist,en_name^node_id", "请输入英文名称|英文名格式只支持字母与数字|英文名重复"],
            ["href", "url", "链接格式不正确"],
        ];
        $data = $request->put();
        $validate = new Validate($rule);
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), 'failed');
        }
        if (!(new Content_get)->save($data, ["id" => $id])) {
            return $this->resultArray('修改失败', 'failed');
        }
        return $this->resultArray("修改成功");
    }

    /**
     * 删除文章
     * @param $id
     * @return array
     */
    public function delete($id)
    {
        return $this->deleteRecord((new \app\common\model\ContentGet()),$id);
    }

}
