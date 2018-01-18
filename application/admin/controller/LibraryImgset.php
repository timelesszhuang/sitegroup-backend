<?php
/**
 * Created by IntelliJ IDEA.
 * User: jingyang
 * Date: 1/17/18
 * Time: 11:03 AM
 */

namespace app\admin\controller;


use think\Controller;
use app\common\controller\Common;
use app\admin\model\LibraryImgset as Library;
use think\Request;
use think\Validate;
use app\common\traits\Obtrait;
use app\common\traits\Osstrait;

class LibraryImgset extends Common
{
    use Obtrait;
    use Osstrait;
    protected $conn='';
    /**
     * 初始化操作
     */
    public function _initialize()
    {
        $this->conn=new Library();
    }

    /**
     * 获取所有爬虫文章
     *
     * @return array
     */
    public function index()
    {
        $request = $this->getLimit();
        $title= $this->request->get('title');
        $where = [];
        $user = $this->getSessionUser();
        $where["node_id"] = $user["user_node_id"];
        $count = $this->conn->where($where)->count();
        $data = $this->conn->limit($request["limit"], $request["rows"])->where($where)->field('id,imgsrc,comefrom,tags,alt,create_time')->order('id desc')->select();
        return $this->resultArray('', '', [
            "total" => $count,
            "rows" => $data
        ]);
    }

    /**
     * 获取某个文章
     * @param $id
     * @return array
     */
    public function read($id)
    {
        return $this->resultArray('','',$this->conn->getOne($id));
    }

    /**
     * 删除指定资源
     * @param  int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        return $this->deleteRecord($this->conn, $id);
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
            ["imgsrc", "require", "请上传图片"],
        ];
        $validate = new Validate($rule);
        $data = $request->post();
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), "failed");
        }

        $library_img_set = $this->conn;
        $library_img_set->batche_add([$data['imgsrc']],$data['tags'],$data['alt'],'selfadd');



        return $this->resultArray("添加成功");
    }
    /**
     * 图片上传到 oss相关操作
     * @access public
     */
    public function imageupload()
    {
        $data = $this->uploadImg("libraryimgset/");
        if ($data['status']) {
            $data["msg"] = "上传成功";
            return $data;
        } else {
            return $this->resultArray('上传失败', 'failed');
        }
    }
}