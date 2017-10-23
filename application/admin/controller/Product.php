<?php

namespace app\admin\controller;

use app\common\controller\Common;

use app\common\traits\Osstrait;
use think\Config;
use think\Request;
use app\common\traits\Obtrait;
use think\Validate;
use app\admin\model\Product as productM;

class Product extends Common
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
        $type_id = $this->request->get('type_id');
        $where = [];
        if (!empty($name)) {
            $where["name"] = ["like", "%$name%"];
        }
        if (!empty($type_id)) {
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
     * @param  \think\Request $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $post = $request->post();
        $rule = [
            ["name", "require", "请输入产品名称"],
            ["summary", "require", "请输入摘要"],
            ["detail", "require", "请输入详情"],
            ["image", 'require', "请上传产品缩略图"],
            ["type_id", 'require', "请上传分类"],
            ['type_name', 'require', "请上传分类名称"]
        ];
        $validate = new Validate($rule);
        if (!$validate->check($post)) {
            return $this->resultArray($validate->getError(), 'failed');
        }
        //本地图片位置
        $type = $this->analyseUrlFileType($post['image']);
        //生成随机的文件名
        $post['image_name'] = $this->formUniqueString() . ".{$type}";
        //图片的ser
        $ser = [];
        if ($post['imgser']) {
            foreach ($post['imgser'] as $v) {
                //分析文件后缀
                $type = $this->analyseUrlFileType($v);
                if ($type) {
                    $img_name = $this->formUniqueString() . ".{$type}";
                }
                $ser[] = [
                    'imgname' => $img_name,
                    'osssrc' => $v,
                ];
            }
        }
        $post['imgser'] = serialize($ser);
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
     * @param  int $id
     * @return \think\Response
     */
    public function read($id)
    {
        $data = (new productM)->where(["id" => $id])->field("create_time,update_time", true)->find();
        $data['imgser'] = unserialize($data->imgser);
        return $this->resultArray('', '', $data);
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
        $post = $request->post();
        $rule = [
            ["name", "require", "请输入产品名称"],
            ["summary", "require", "请输入摘要"],
            ["detail", "require", "请输入详情"],
            ["type_id", 'require', "请上传分类"],
            ['type_name', 'require', "请上传分类名称"]
        ];
        $validate = new Validate($rule);
        if (!$validate->check($post)) {
            return $this->resultArray($validate->getError(), 'failed');
        }
        if (!empty($post["image"])) {
            $model = (new productM)->where(["id" => $id])->find();
            $file = ROOT_PATH . "public/static/" . $model->image;
            if (file_exists($file)) {
                unlink($file);
            }
            $post["base64"] = $this->base64EncodeImage(ROOT_PATH . "public/static/" . $post['image']);
            // 如果是base64的图片
            if (preg_match('/(data:\s*image\/(\w+);base64,)/', $post["base64"], $result)) {
                $type = $result[2];
                $post['image_name'] = md5(uniqid(rand(), true)) . ".$type";
            }
        }
        if (!(new productM)->save($post, ["id" => $id])) {
            return $this->resultArray('修改失败', 'failed');
        }
        $this->open_start('正在修改中');
        $where['type_id'] = $post['type_id'];
        $where['flag'] = 5;
        $menu = (new \app\admin\model\Menu())->where($where)->select();
//        dump($menu);die;
        $user = $this->getSessionUser();
        $wh['node_id'] = $user['user_node_id'];
        $sitedata = \app\admin\model\Site::where($wh)->select();
//        dump($sitedata);
        $arr = [];
        $ar = [];
        foreach ($menu as $k => $v) {
            $arr[] = $v['id'];
            foreach ($sitedata as $kk => $vv) {
                $a = strstr($vv["menu"], "," . $v["id"] . ",");
                if ($a) {
                    $Site = new \app\admin\model\Site();
                    $dat = $Site->where('id', 'in', $vv['id'])->field('url')->select();
                    foreach ($dat as $key => $value) {
                        $send = [
                            "id" => $post['id'],
                            "searchType" => 'product',
                            "type" => $post['type_id']
                        ];
//                        dump($send);
//                        dump($value['url']."/index.php/generateHtml");die;
                        $this->curl_post($value['url'] . "/index.php/generateHtml", $send);
                    }
                }
            }

        }
    }

    /**
     * 删除指定资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }


    /**
     * 上传产品主图
     * @return array
     */
    public function uploadImage()
    {
        //产品的主图
        $dest_dir = 'product/mainimg/';
        $endpoint = Config::get('oss.endpoint');
        $bucket = Config::get('oss.bucket');
        $file = request()->file('img');
        $localfile_path = ROOT_PATH . 'public/upload/';
        $fileInfo = $file->move($localfile_path);
        $object = $dest_dir . $fileInfo->getSaveName();
        $put_info = $this->ossPutObject($object, $localfile_path . $fileInfo->getSaveName());
        $url = '';
        $status = false;
        $msg = '上传失败';
        if ($put_info['status']) {
            $msg = '上传成功';
            $status = true;
            $url = sprintf("https://%s.%s/%s", $bucket, $endpoint, $object);
        }
        return [
            "url" => $url,
            'status' => $status,
            'msg' => $msg,
        ];
    }

    /**
     * 上传产品图片
     * @access public
     */
    public function uploadImgSer()
    {
        //产品的主图
        $dest_dir = 'product/imgser/';
        $endpoint = Config::get('oss.endpoint');
        $bucket = Config::get('oss.bucket');
        $file = request()->file('img');
        $localfile_path = ROOT_PATH . 'public/upload/';
        $fileInfo = $file->move($localfile_path);
        $object = $dest_dir . $fileInfo->getSaveName();
        $put_info = $this->ossPutObject($object, $localfile_path . $fileInfo->getSaveName());
        $url = '';
        $status = false;
        $msg = '上传失败';
        if ($put_info['status']) {
            $msg = '上传成功';
            $status = true;
            $url = sprintf("https://%s.%s/%s", $bucket, $endpoint, $object);
        }
        return [
            "url" => $url,
            'status' => $status,
            'msg' => $msg,
        ];
    }


}
