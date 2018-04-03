<?php

namespace app\wx\controller;

use app\common\controller\Common;
use app\common\controller\CommonLogin;
use app\common\exception\ProcessException;
use app\common\model\LibraryImgset;
use app\common\traits\Osstrait;
use think\Config;
use think\Validate;
use think\Request;
use app\common\traits\Obtrait;
use app\wx\model\WxImglist as this_model;

class ImgList extends Common
{
    use Obtrait;
    use Osstrait;

    public function __construct()
    {
        parent::__construct();
        $this->model = new this_model();
    }

    /**
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $request = $this->getLimit();
        $name = $this->request->get('name');
        $app_id = $this->request->get('app_id');
        if ($name) {
            $where["name"] = ["like", "%$name%"];
        }
        if(!$app_id){
            $app_id = (new \app\wx\model\WxSmallApp())->getAppId($this->getSessionUserInfo()["node_id"]);
        }
        $where["app_id"] = $app_id;
        $data = $this->model->getImgList($request["limit"], $request["rows"], $where);
        return $this->resultArray('', '', $data);
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request $request
     * @return array
     */
    public function save(Request $request)
    {
        try {
            $rule = [
                ["name", "require", "请输入图集名称"],
                ["en_name", "require|alphaNum|unique:imglist,en_name^node_id", "请输入图集英文名称|英文名格式只支持字母与数字|英文名重复"],
            ];
            $validate = new Validate($rule);
            $data = $request->post();
            $user = $this->getSessionUserInfo();
            $data['node_id'] = $user['node_id'];
            $data['imgser'] = '';
            if (!$validate->check($data)) {
                Common::processException($validate->getError());
            }
            if (!$this->model->create($data)) {
                Common::processException('添加失败');
            }
            return $this->resultArray('添加成功');
        } catch (ProcessException $e) {
            return $this->resultArray('failed', $e->getMessage());
        }
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
        try {
            $rule = [
                ["name", "require", "请输入图集名称"],
                ["en_name", "require|alphaNum|unique:imglist,en_name^node_id", "请输入图集英文名称|英文名格式只支持字母与数字|英文名重复"],
            ];
            $data = $request->put();
            $validate = new Validate($rule);
            if (!$validate->check($data)) {
                Common::processException($validate->getError());
            }
            if (!$this->model->save($data, ["id" => $id])) {
                Common::processException('修改失败');
            }
            return $this->resultArray('修改成功');
        } catch (ProcessException $e) {
            return $this->resultArray('failed', $e->getMessage());
        }
    }

    /**
     * 删除指定资源
     * @param  int $id
     * @param $status
     * @return array
     */
    public function changeStatus($id, $status)
    {
        $data['status'] = $status;
        $msg = '禁用';
        if($status == 10){
            $msg = '启用';
        }
        if (!$this->model->save($data, ["id" => $id])) {
            return $this->resultArray('failed', $msg.'失败');
        }
        return $this->resultArray($msg."成功");
    }

    /**
     * 获取图集的图片src
     * @param $id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getImgSer($id)
    {
        $data = $this->model->where(["id" => $id])->field("id,imgser")->find()->toArray();
        $imgser = [];
        if ($data['imgser']) {
            $imgser = unserialize($data['imgser']);
            unset($data['imgser']);
        }
        $data['imglist'] = $imgser;
        return $this->resultArray($data);
    }

    public function getImgSerByEnName($enmane)
    {
        $app_id = $this->request->get('app_id');
        $where = ["en_name" => $enmane];
        if(!$app_id){
            $app_id = (new \app\wx\model\WxSmallApp())->getAppId($this->getSessionUserInfo()["node_id"]);
        }
        $where["app_id"] = $app_id;
        $data = $this->model->where(["en_name" => $enmane])->field("id,imgser")->find()->toArray();
        $imgser = [];
        if ($data['imgser']) {
            $imgser = unserialize($data['imgser']);
            unset($data['imgser']);
        }
        $data['imglist'] = $imgser;
        return $this->resultArray($data);
    }

    /**
     * 修改 添加图片的Imgser 区分根据 $index 如果没有index是添加;
     * @access public
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function uploadImgSer()
    {
        try {
            $id = \request()->post('id');
            $index = \request()->post('index');
            $title = \request()->post('title');
            $link = \request()->post('link');
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
            if (!$title) {
                Common::processException('标题不能为空');
            }
            $imgser = [];
            if ($put_info['status']) {
                //上传成功之后需要删除掉之前的存储的对象
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
                $deleteobject = '';
                $dest = [
                    'imgname' => $img_name,
                    'osssrc' => $url,
                    'title' => $title,
                    'link' => $link,
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
                    //$this->ossDeleteObject($deleteobject);
                }
            }
            $imglist = [];
            foreach ($imgser as $v) {
                $imglist[] = $v['osssrc'];
            }

            $library_img_set = new LibraryImgset();
            $library_img_set->batche_add($imglist, [], '', 'product');

            return $this->resultArray('success','上传成功',$imgser);
        } catch (ProcessException $e) {
            return $this->resultArray('failed', $e->getMessage());
        }
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
        if ($data->imgser) {
            $imgser = unserialize($data->imgser);
            $deleteobject = $imgser[$index]['osssrc'];
            unset($imgser[$index]);
            $imgser = array_values($imgser);
        }
        $data->imgser = serialize($imgser);
        $data->save();
        //需要去服务器上删除已经被替换的对象
        if ($deleteobject) {
            //需要截取掉之前的路径
            //$result = $this->ossDeleteObject($deleteobject);
        }
        return $this->resultArray('success', '删除完成', $imgser);
    }
    /**
     * @param $id
     * @return array
     */
    public function read($id)
    {
        return $this->getread((new \app\common\model\Imglist()), $id);
    }


    /**
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function saveInfo()
    {
        try {
            $rule = [
                ["id", "require", "参数错误"],
                ["index", "require", "参数错误"],
                ["title", "require", "请输入标题"],
                ["link", "require", "请输入链接"],
            ];
            $validate = new Validate($rule);
            $data = \request()->post();
            if (!$validate->check($data)) {
                Common::processException($validate->getError());
            }
            $id = \request()->post('id');
            $index = \request()->post('index');
            $title = \request()->post('title');
            $link = \request()->post('link');
            $data = $this->model->where(["id" => $id])->field("id,imgser")->find();
            $imgser = [];
            if (!empty($data->imgser)) {
                if ($data->imgser) {
                    $imgser = unserialize($data->imgser);
                    $imgser[$index]['title'] = $title;
                    $imgser[$index]['link'] = $link;
                    $imgser = array_values($imgser);
                }
            }
            $data->imgser = serialize($imgser);
            $data->save();
            return $this->resultArray('修改成功',$imgser);
        } catch (ProcessException $e) {
            return $this->resultArray('failed', $e->getMessage());
        }
    }
}
