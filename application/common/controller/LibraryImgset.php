<?php
/**
 * Created by IntelliJ IDEA.
 * User: jingyang
 * Date: 1/17/18
 * Time: 11:03 AM
 */

namespace app\common\controller;


use app\common\model\LibraryImgset as this_model;
use think\Request;
use think\Validate;
use app\common\traits\Obtrait;
use app\common\traits\Osstrait;

class LibraryImgset extends CommonLogin
{
    use Obtrait;
    use Osstrait;

    /**
     * 获取所有爬虫文章
     *
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $request = $this->getLimit();
        $type = $this->request->get('type');
        $tag_id = $this->request->get('tag_id');
        $where = [];
        if ($type) {
            $where['comefrom'] = $type;
        }
        if ($tag_id) {
            $where['tags'] = ['like', "%,$tag_id,%"];
        }
        $user = $this->getSessionUserInfo();
        $where["node_id"] = $user["node_id"];
        $count = (new this_model)->where($where)->count();
        $data = (new this_model)->limit($request["limit"], $request["rows"])->where($where)->field('id,imgsrc,comefrom,tags,alt,create_time')->order('id desc')->select();
        return $this->resultArray(["total" => $count, "rows" => $data]);
    }

    /**
     * 获取某个文章
     * @param $id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function read($id)
    {
        return $this->resultArray($this->getread((new this_model), $id));
    }

    /**
     * 删除指定资源
     * @param  int $id
     * @return array
     */
    public function delete($id)
    {
        return $this->deleteRecord((new this_model), $id);
    }

    /**
     * 保存新建的资源
     * @param  \think\Request $request
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function save(Request $request)
    {
        $rule = [
            ["imgsrc", "require", "请上传图片"],
        ];
        $validate = new Validate($rule);
        $data = $request->post();
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), "failed");
        }

        $library_img_set = (new this_model);
        $library_img_set->batche_add([$data['imgsrc']], $data['tags'], $data['alt'], 'selfadd');


        return $this->resultArray("添加成功");
    }
}