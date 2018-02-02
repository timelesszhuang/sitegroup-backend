<?php
/**
 * Created by IntelliJ IDEA.
 * User: jingyang
 * Date: 1/17/18
 * Time: 11:03 AM
 */

namespace app\common\controller;


use app\common\exception\ProcessException;
use app\common\model\LibraryImgset as this_model;
use think\Request;
use think\Validate;
use app\common\traits\Obtrait;
use app\common\traits\Osstrait;

class LibraryImgset extends CommonLogin
{
    use Obtrait;
    use Osstrait;

    public function __construct()
    {
        parent::__construct();
        $this->model = new this_model();
    }

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
        $count = $this->model->where($where)->count();
        $data = $this->model->limit($request["limit"], $request["rows"])->where($where)->field('id,imgsrc,comefrom,tags,alt,create_time')->order('id desc')->select();
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
        return $this->resultArray($this->getread($this->model, $id));
    }

    /**
     * 删除指定资源
     * @param  int $id
     * @return array
     */
    public function delete($id)
    {
        return $this->deleteRecord($this->model, $id);
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
        try {
            $rule = [
                ["imgsrc", "require", "请上传图片"],
            ];
            $validate = new Validate($rule);
            $data = $request->post();
            if (!$validate->check($data)) {
                Common::processException($validate->getError());
            }
            $library_img_set = $this->model;
            $library_img_set->batche_add([$data['imgsrc']], $data['tags'], $data['alt'], 'selfadd');
            return $this->resultArray("添加成功");
        } catch (ProcessException $e) {
        return $this->resultArray('failed', $e->getMessage());
        }
    }
}