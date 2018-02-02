<?php

namespace app\admin\controller;

use app\common\controller\Common;
use app\common\controller\CommonLogin;
use app\common\exception\ProcessException;
use app\common\model\Menu;
use app\common\model\Site;
use app\common\traits\Osstrait;
use think\Config;
use think\Model;
use think\Request;
use app\common\traits\Obtrait;
use think\Validate;
use app\common\model\LibraryImgset;

class Product extends CommonLogin
{
    use Obtrait;
    use Osstrait;

    public function __construct()
    {
        parent::__construct();
        $this->model = new \app\common\model\Product();
    }

    /**
     * 显示资源列表
     *
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
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
        $user = $this->getSessionUserInfo();
        if ($user['user_type_name'] == 'node') {
            $where["node_id"] = $user["node_id"];
        } else {
            $type_ids = (new Menu())->getSiteTypeIds($user['menu'], 5);
            $where['type_id'] = ['in', $type_ids];
        }
        $data = $this->model->getAll($request["limit"], $request["rows"], $where);
        return $this->resultArray($data);

    }

    /**
     * 保存新建的资源
     *
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
                Common::processException($validate->getError());
            }
            //本地图片位置
            $type = $this->analyseUrlFileType($post['image']);
            //生成随机的文件名
            $post['image_name'] = $this->formUniqueString() . ".{$type}";
            $post['imgser'] = '';
            $user = $this->getSessionUserInfo();
            $post["node_id"] = $user["node_id"];
            $model = $this->model;
            $library_img_tags = [];
            if (isset($post['tag_id']) && is_array($post['tag_id'])) {
                $library_img_tags = $post['tag_id'];
                $post['tags'] = ',' . implode(',', $post['tag_id']) . ',';
            } else {
                $post['tags'] = "";
            }
            unset($post['tag_id']);
            $library_img_set = new LibraryImgset();
            $src_list = $library_img_set->getList($post['detail']);
            if ($post['image']) {
                $src_list[] = $post['image'];
            }
            $library_img_set->batche_add($src_list, $library_img_tags, $post['title'], 'product');
            if (!$model->save($post)) {
                Common::processException('添加失败');
            }
            return $this->resultArray('添加成功');
        } catch (ProcessException $e) {
            return $this->resultArray('failed', $e->getMessage());
        }
    }

    /**
     * 显示指定的资源
     *
     * @param  int $id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function read($id)
    {
        $data = $this->model->where(["id" => $id])->field("create_time,update_time,imgser", true)->find();
        $data['tags'] = implode(',', array_filter(explode(',', $data['tags'])));
        return $this->resultArray('', '', $data);
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request $request
     * @param  int $id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function update(Request $request, $id)
    {
        try {
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
                Common::processException($validate->getError());
            }
            if (!empty($post["image"])) {
                $model = $this->model->where(["id" => $id])->find();
                if (isset($model->image)) {
                    if ($model->image != $post['image']) {
                        //现在的图片跟之前的不一致 需要删除之前的oss图片
                        $pre_src = $model->image;
                        if ($pre_src) {
                            //原图存在则去oss删除文件
                            $this->ossDeleteObject($pre_src);
                        }
                        $type = $this->analyseUrlFileType($post['image']);
                        $post['image_name'] = $this->formUniqueString() . ".$type";
                    }
                }
                //要静态化的文件名
            }
            $library_img_tags = [];
            if (isset($post['tag_id']) && is_array($post['tag_id'])) {
                $library_img_tags = $post['tag_id'];
                $post['tags'] = ',' . implode(',', $post['tag_id']) . ',';
            } else {
                $post['tags'] = "";
            }
            unset($post['tag_id']);
            if (!$this->model->save($post, ["id" => $id])) {
                Common::processException("修改失败");
            }

            $library_img_set = new LibraryImgset();
            $src_list = $library_img_set->getList($post['detail']);
            if ($post['image']) {
                $src_list[] = $post['image'];
            }
            $library_img_set->batche_add($src_list, $library_img_tags, $post['title'], 'product');

            //正在修改中 首先提示前台已经生成完成然后去服务器上面 重新生成新的页面
            $this->open_start('正在修改中');
            $type_id = $post['type_id'];
            $sitedata = $this->getProductSite($type_id);
            if (array_key_exists('status', $sitedata)) {
                return $sitedata;
            }
            foreach ($sitedata as $kk => $vv) {
                $send = [
                    "id" => $post['id'],
                    "searchType" => 'product',
                ];
                $this->curl_post($vv['url'] . "/index.php/generateHtml", $send);
            }
            return $this->resultArray();
        } catch (ProcessException $e) {
            return $this->resultArray('failed', $e->getMessage());
        }
    }

    /**
     * 删除指定资源
     *
     * @param  int $id
     * @return void
     */
    public function delete($id)
    {
        //
    }


    /**
     * 获取产品多图状态下的图片src
     * @access public
     * @param $id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getImgSer($id)
    {
        $data = $this->model->where(["id" => $id])->field("id,imgser")->find()->toArray();
        $list = [];
        if ($data['imgser']) {
            $imgser = unserialize($data['imgser']);
            foreach ($imgser as $v) {
                $list[] = $v['osssrc'];
            }
            unset($data['imgser']);
        }
        $data['imglist'] = $list;
        return $this->resultArray($data);
    }

    /***
     * 修改 添加图片的Imgser 区分根据 $index 如果没有index是添加;
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
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
            $data = $this->model->where(["id" => $id])->field("id,imgser")->find();
            /** @var string $deleteobject */
            $deleteobject = '';
            $dest = [
                'imgname' => $img_name,
                'osssrc' => $url,
            ];
            if (!empty($data->imgser)) {
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

        $library_img_set = new LibraryImgset();
        $library_img_set->batche_add($imglist, [], '', 'product');

        return $this->resultArray($status, $msg, $imglist);
    }

    /**
     * 删除图片中个别的imgser
     * @access public
     * @param $id
     * @param $index
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function deleteImgser($id, $index)
    {
        $data = $this->model->where(["id" => $id])->field("id,imgser")->find();
        $deleteobject = '';
        $imgser = [];
        if (!empty($data->imgser)) {
            if ($data->imgser) {
                $imgser = unserialize($data->imgser);
                $deleteobject = $imgser[$index]['osssrc'];
                unset($imgser[$index]);
                $imgser = array_values($imgser);
            }
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
            $this->ossDeleteObject($deleteobject);
        }
        return $this->resultArray('success', '删除产品图片完成', $imglist);
    }

    /***
     * 获取某个网站对应的产品
     * @param $type_id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function getProductSite($type_id)
    {
        try {
            $where['flag'] = 5;
            $model_menu = (new Menu());
            $menu = $model_menu->where(function ($query) use ($where) {
                /** @var Model $query */
                $query->where($where);
            })->where(function ($query) use ($type_id) {
                /** @var Model $query */
                $query->where('type_id', ['=', $type_id], ['like', "%,$type_id,%"], 'or');
            })->select();
            if (!$menu) {
                Common::processException('产品分类没有菜单选中页面，暂时不能预览。');
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
            $sitedata = (new Site())->where($map)->field('id,site_name,url')->select();
            return $this->resultArray($sitedata);
        } catch (ProcessException $e) {
            return $this->resultArray('failed', $e->getMessage());
        }
    }

    /**
     * @return array
     * 产品页面预览
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function productShowHtml()
    {
        $data = $this->request->post();
        $sitedata = $this->getProductSite($data['type_id']);
        if (array_key_exists('status', $sitedata)) {
            return $sitedata;
        }
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
