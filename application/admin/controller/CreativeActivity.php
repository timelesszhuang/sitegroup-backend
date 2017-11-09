<?php

namespace app\admin\controller;

use app\common\controller\Common;
use think\Request;
use app\common\model\CreativeActivity as creative;
use app\common\traits\Osstrait;
use think\Validate;
use app\common\traits\Obtrait;
class CreativeActivity extends Common
{
    use Obtrait;
    use Osstrait;
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $request = $this->getLimit();
        $name = $this->request->get('name');
        $id = $this->request->get('id');
        $where = [];
        if (!empty($name)) {
            $where["name"] = ["like", "%$name%"];
        }
        if (!empty($id)) {
            $where["id"] = $id;
        }
        $user = $this->getSessionUser();
        $where["node_id"] = $user["user_node_id"];
        $data = (new creative())->getAll($request["limit"], $request["rows"], $where);
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
        $rule=[
            ["title","require","请输入标题"],
            ["oss_img_src","require","请传递封面"],
            ["img_name","require","请上传图片名"],
            ["keywords","require","请输入页面关键词"],
            ["summary","require","请输入页面描述"],
            ["content","require",'请输入活动详情'],
        ];
        $validate=new Validate($rule);
        $data=$request->post();
        if(!$validate->check($data)){
            return $this->resultArray($validate->getError(),"failed");
        }
        if(creative::create($data)){
            return $this->resultArray("添加成功");
        }
        return $this->resultArray("添加失败","failed");
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        return $this->getread((new creative), $id);
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

    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }

    /**
     * 上传活动缩略图
     * @return array
     */
    public function imageUpload()
    {
        return $this->uploadImg("activity/");
    }

    /**
     * 外站添加
     * @param Request $request
     * @return array
     */
    public function storyOut(Request $request)
    {
       $rule=[
           ["title","require","请输入标题"],
           ["oss_img_src","require","请先上传图片"],
           ["url","require","请输入外部链接"]
       ];
       $validate=new Validate($rule);
       $data=$request->post();
       if(!$validate->check($data)){
           return $this->resultArray($validate->getError());
       }
        $user = $this->getSessionUser();
        $data["node_id"] = $user["user_node_id"];
        //本地图片位置
        $type = $this->analyseUrlFileType($data['oss_img_src']);
        //生成随机的文件名
        $data['image_name'] = $this->formUniqueString() . ".{$type}";
       if(creative::create($data)){
           return $this->resultArray("添加成功");
       }
       return $this->resultArray("添加失败","failed");
    }

    /**
     * 修改外部活动
     * @param $id
     * @return array
     */
    public function saveOut($id)
    {
        $rule=[
            ["title","require","请输入标题"],
            ["oss_img_src","require","请先上传图片"],
            ["url","require","请输入外部链接"]
        ];
        $request=Request::instance();
        $validate=new Validate($rule);
        $data=$request->post();
        if(!$validate->check($data)){
            return $this->resultArray($validate->getError(),"failed");
        }
        if(creative::update($data,["id"=>$id])){
            return $this->resultArray("修改成功");
        }
        return $this->resultArray("修改失败","failed");
    }

}
