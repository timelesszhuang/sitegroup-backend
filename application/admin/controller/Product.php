<?php

namespace app\admin\controller;

use app\common\controller\Common;

use think\Request;
use app\common\traits\Obtrait;
use think\Validate;
use app\admin\model\Product as productM;
class Product extends Common
{
    use Obtrait;
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $request = $this->getLimit();
        $name = $this->request->get('name');
        $type_id=$this->request->get('type_id');
        $where = [];
        if (!empty($name)) {
            $where["name"] = ["like", "%$name%"];
        }
        if(!empty($type_id)){
            $where["type_id"] = $type_id;
        }
        $user = $this->getSessionUser();
        $where["node_id"] = $user["user_node_id"];
        $data = (new productM())->getAll($request["limit"], $request["rows"], $where);
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
        $post = $request->post();
        $rule = [
            ["name", "require", "请输入产品名称"],
            ["summary", "require", "请输入摘要"],
            ["detail", "require", "请输入详情"],
            ["image",'require',"请上传图片"],
            ["type_id",'require',"请上传分类"],
            ['type_name','require',"请上传分类名称"]

        ];
        $validate = new Validate($rule);
        if (!$validate->check($post)) {
            return $this->resultArray($validate->getError(), 'failed');
        }
        $post["base64"]=$this->base64EncodeImage("static/".$post['image']);
        // 如果是base64的图片
        if (preg_match('/(data:\s*image\/(\w+);base64,)/',$post["base64"],$result)){
            $type = $result[2];
            $post['image_name']=md5(uniqid(rand(), true)).".$type";
        }
        $user = $this->getSessionUser();
        $post["node_id"] = $user["user_node_id"];
        $model = new productM();
        $model->save($post);
        if ($model->id) {
            return $this->resultArray("添加成功");
        }
        return $this->resultArray('添加失败', 'failed');
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        return $this->resultArray('', '', (new productM)->where(["id" => $id])->field("create_time,update_time", true)->find());
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
        $post = $request->post();
        $rule = [
            ["name", "require", "请输入产品名称"],
            ["summary", "require", "请输入摘要"],
            ["detail", "require", "请输入详情"],
            ["type_id",'require',"请上传分类"],
            ['type_name','require',"请上传分类名称"]

        ];
        $validate = new Validate($rule);
        if (!$validate->check($post)) {
            return $this->resultArray($validate->getError(), 'failed');
        }
        if(!empty($post["image"])){
            $model=(new productM)->where(["id"=>$id])->find();
            $file=ROOT_PATH."public/static/".$model->image;
            if(file_exists($file)){
                unlink($file);
            }
            $post["base64"]=$this->base64EncodeImage(ROOT_PATH."public/static/".$post['image']);
            // 如果是base64的图片
            if (preg_match('/(data:\s*image\/(\w+);base64,)/',$post["base64"],$result)){
                $type = $result[2];
                $post['image_name']=md5(uniqid(rand(), true)).".$type";
            }
        }
        if (!(new productM)->save($post, ["id" => $id])) {
            return $this->resultArray('修改失败', 'failed');
        }
        $this->open_start('正在修改中');
        $where['type_id'] = $post['type_id'];
        $where['flag'] = 5;
        $menu = (new \app\admin\model\Menu())->where($where)->select();
        dump($menu);die;
        $user = $this->getSessionUser();
        $wh['node_id'] = $user['user_node_id'];
        $sitedata = \app\admin\model\Site::where($wh)->select();
//        dump($sitedata);
        $arr = [];
        $ar = [];
        foreach ($menu as $k => $v) {
            $arr[] = $v['id'];
            foreach ($sitedata as $kk => $vv) {
                $a=strstr($vv["menu"],",".$v["id"].",");
                if($a){
                    $Site = new \app\admin\model\Site();
                    $dat = $Site->where('id','in',$vv['id'])->field('url')->select();
                    foreach ($dat as $key=>$value){
                        $send = [
                            "id" => $post['id'],
                            "searchType" => 'product',
                            "type" => $post['type_id']
                        ];
//                        dump($send);
//                        dump($value['url']."/index.php/generateHtml");die;
                        $this->curl_post($value['url']."/index.php/generateHtml",$send);
                    }
                }
            }

        }
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
     * 上传图片文件
     * @return array
     */
    public function uploadImage()
    {
        $file = request()->file('file_name');
        $info = $file->move(ROOT_PATH . 'public/static/');
        if ($info) {
            return $this->resultArray('上传成功', '', $info->getSaveName());
        } else {
            // 上传失败获取错误信息
            return $this->resultArray('上传失败', 'failed', $info->getError());
        }
    }
}
