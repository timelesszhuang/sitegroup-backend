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
        $post['imgser'] = '';
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
        $data = (new productM)->where(["id" => $id])->field("create_time,update_time,imgser", true)->find();
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
            if ($model->image != $post['image']) {
                //现在的图片跟之前的不一致 需要删除之前的oss图片
                $pre_src = $model->image;
                if ($pre_src) {
                    //原图存在则去oss删除文件
                    $this->ossDeleteObject($pre_src);
                }
            }
            //要静态化的文件名
            $type = $this->analyseUrlFileType($post['image']);
            $post['image_name'] = $this->formUniqueString() . ".$type";
        }
        if (!(new productM)->save($post, ["id" => $id])) {
            return $this->resultArray('修改失败', 'failed');
        }
        //正在修改中 首先提示前台已经生成完成然后去服务器上面 重新生成新的页面
        $this->open_start('正在修改中');
        $type_id = $post['type_id'];
        $sitedata = $this->getProductSite($type_id);
        foreach ($sitedata as $kk => $vv) {
            $send = [
                "id" => $post['id'],
                "searchType" => 'product',
                "type" => $post['type_id'],
            ];
            $this->curl_post($vv['url'] . "/index.php/generateHtml", $send);
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
     * 获取产品多图状态下的图片src
     * @access public
     */
    public function getImgSer($id)
    {
        $data = (new productM)->where(["id" => $id])->field("id,imgser")->find()->toArray();
        $list = [];
        if ($data['imgser']) {
            $imgser = unserialize($data['imgser']);
            foreach ($imgser as $v) {
                $list[] = $v['osssrc'];
            }
            unset($data['imgser']);
        }
        $data['imglist'] = $list;
        return $this->resultArray('', '', $data);
    }

    /**
     * 修改 添加图片的Imgser 区分根据 $index 如果没有index是添加;
     * @access public
     * @todo 1、上传图片到oss
     *       2、需要删除原来的object数据
     *       3、更新数据库中的 字段
     */
    public function uploadImgSer()
    {
        $id = \request()->post('id');
        $index = \request()->post('index');
        //产品的其他图片
        $dest_dir = 'product/imgser/';
        $endpoint = Config::get('oss.endpoint');
        $bucket = Config::get('oss.bucket');
        if ($index == NULL) {
            $file = request()->file('addimg');
            $imgtype = 'add';
        } else {
            $file = request()->file('updateimg');
            $imgtype = 'edit';
        }
        $localfile_path = ROOT_PATH . 'public/upload/';
        $fileInfo = $file->move($localfile_path);
        $localfile = $localfile_path . $fileInfo->getSaveName();
        $object = $dest_dir . $fileInfo->getSaveName();
        $put_info = $this->ossPutObject($object, $localfile);
        unlink($localfile);
        $url = '';
        $status = false;
        $msg = '上传失败';
        $imgser = [];
        if ($put_info['status']) {
            //上传成功之后需要删除掉之前的存储的对象
            $msg = '上传成功';
            $status = true;
            $url = sprintf("https://%s.%s/%s", $bucket, $endpoint, $object);
            //分析文件后缀
            $type = $this->analyseUrlFileType($url);
            if ($type) {
                $img_name = $this->formUniqueString() . ".{$type}";
            } else {
                //不带后缀的情况
                $img_name = $this->formUniqueString();
            }
            $data = (new productM)->where(["id" => $id])->field("id,imgser")->find();
            $deleteobject = '';
            $dest = [
                'imgname' => $img_name,
                'osssrc' => $url,
            ];
            if ($data->imgser) {
                $imgser = unserialize($data->imgser);
                if ($imgtype == 'edit') {
                    foreach ($imgser as $k => $v) {
                        if ($k == $index) {
                            $imgser[$k] = $dest;
                            $deleteobject = $v['osssrc'];
                            break;
                        }
                    }
                } else {
                    //是添加的情况
                    if ($index !== 0 && !$index) {
                        array_push($imgser, $dest);
                    }
                }
            } else {
                //表示第一次是空的
                $imgser[] = $dest;
            }
            $data->imgser = serialize($imgser);
            $data->save();
            //需要去服务器上删除已经被替换的对象
            if ($deleteobject) {
                //需要截取掉之前的路径
                $this->ossDeleteObject($deleteobject);
            }
        }
        $imglist = [];
        foreach ($imgser as $v) {
            $imglist[] = $v['osssrc'];
        }
        return [
            "imglist" => $imglist,
            'status' => $status,
            'msg' => $msg,
        ];
    }

    /**
     * 删除图片中个别的imgser
     * @access public
     */
    public function deleteImgser($id, $index)
    {
        $data = (new productM)->where(["id" => $id])->field("id,imgser")->find();
        $deleteobject = '';
        $imgser = [];
        if ($data->imgser) {
            $imgser = unserialize($data->imgser);
            $deleteobject = $imgser[$index]['osssrc'];
            unset($imgser[$index]);
            $imgser = array_values($imgser);
        }
        $data->imgser = serialize($imgser);
        $imglist = [];
        foreach ($imgser as $v) {
            $imglist[] = $v['osssrc'];
        }
        $data->save();
        //需要去服务器上删除已经被替换的对象
        if ($deleteobject) {
            //需要截取掉之前的路径
            $result = $this->ossDeleteObject($deleteobject);
        }
        return $this->resultArray('删除产品图片完成', '', $imglist);
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
        $localfile = $localfile_path . $fileInfo->getSaveName();
        $put_info = $this->ossPutObject($object, $localfile);
        unlink($localfile);
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

    /***
     * 获取某个网站对应的产品
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function getProductSite($type_id)
    {
        $where['flag'] = 5;
        $model_menu = (new \app\admin\model\Menu());
        $menu = $model_menu->where(function($query)use($where){
            $query->where($where);
        })->where(function($query)use($type_id){
            $query->where('type_id',['=',$type_id],['like',"%,$type_id,%"],'or');
        })->select();
        if (!$menu) {
            return $this->resultArray('产品分类没有菜单选中页面，暂时不能预览。', 'failed');
        }
        //一个菜单有可能被多个站点选择 site表中只会存储第一级别的菜单 需要找出当前的pid=0的父级菜单
        $pid = [];
        foreach ($menu as $k => $v) {
            $path = $v['path'];
            //该菜单的跟
            if ($path) {
                //获取第一级别的菜单
                $pid[] = array_values(array_filter(explode(',', $path)))[0];
            } else {
                $pid[] = $v['id'];
            }
        }
        $pid = array_unique($pid);
        //查询选择当前菜单的站点相关信息
        $map = '';
        foreach ($pid as $k => $v) {
            $permap = " menu like ',%$v%,' ";
            if ($k == 0) {
                $map = $permap;
                continue;
            }
            $map .= ' or ' . $permap;
        }
        $sitedata = \app\admin\model\Site::where($map)->field('id,site_name,url')->select();
        return $sitedata;
    }

    /**
     * @return array
     * 产品页面预览
     */
    public function productshowhtml()
    {
        $data = $this->request->post();
        $sitedata = $this->getProductSite($data['type_id']);
        foreach ($sitedata as $kk => $vv) {
            $showhtml[] = [
                'url' => $vv['url'] . '/preview/product/' . $data['id'] . '.html',
                'site_name' => $vv['site_name'],
            ];
        }
        if (!empty($showhtml)) {
            return $this->resultArray('', '', $showhtml);
        } else {
            return $this->resultArray('当前文章对应的菜单页面没有站点选择，暂时不能预览。', 'failed');
        }
    }
}
