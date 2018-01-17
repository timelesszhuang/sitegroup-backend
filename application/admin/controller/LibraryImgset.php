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

class LibraryImgset extends Common
{
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
        if (!empty($title)) {
            $where["title"] = ["like", "%$title%"];
        }
        $data = $this->conn->getArticle($request["limit"], $request["rows"], $where);
        return $this->resultArray('', '', $data);
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
            ["title", "require", "请输入标题"],
            ["content", "require", "请输入内容"],
            ["articletype_id", "require", "请选择文章分类"],
//            ["tag_id", "require", "请选择标签"],
        ];
        $validate = new Validate($rule);
        $data = $request->post();
        $user = $this->getSessionUser();
        $data['node_id'] = $user['user_node_id'];
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), "failed");
        }
        $library_img_tags = [];
        if(isset($data['tag_id'])&&is_array($data['tag_id'])){
            $library_img_tags = $data['tag_id'];
            $data['tags']=','.implode(',',$data['tag_id']).',';
        }else{
            $data['tags']="";
        }
        unset($data['tag_id']);
        if (!\app\admin\model\Article::create($data)) {
            return $this->resultArray("添加失败", "failed");
        }


        $library_img_set = new LibraryImgset();
        $src_list = $library_img_set->getList($data['content']);
        if($data['thumbnails']){
            $src_list[]=$data['thumbnails'];
        }
        $library_img_set->batche_add($src_list,$library_img_tags,$data['title'],'article');



        return $this->resultArray("添加成功");
    }
}