<?php

namespace app\admin\controller;

use app\common\controller\Common;
use think\Config;
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
     * @param  \think\Request $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $rule = [
            ["title", "require", "请输入标题"],
            ["oss_img_src", "require", "请传递封面"],
            ["keywords", "require", "请输入页面关键词"],
            ["summary", "require", "请输入页面描述"],
            ["content", "require", '请输入活动详情'],
        ];
        $validate = new Validate($rule);
        $data = $request->post();
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), "failed");
        }
        $user = $this->getSessionUser();
        $data["node_id"] = $user["user_node_id"];
        //本地图片位置
        $type = $this->analyseUrlFileType($data['oss_img_src']);
        //生成随机的文件名
        $data['img_name'] = $this->formUniqueString() . ".{$type}";
        if (creative::create($data)) {
            return $this->resultArray("添加成功");
        }
        return $this->resultArray("添加失败", "failed");
    }

    /**
     * 显示指定的资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function read($id)
    {
        return $this->getread((new creative), $id);
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
        $rule = [
            ["title", "require", "请输入标题"],
            ["oss_img_src", "require", "请传递封面"],
            ["img_name", "require", "请上传图片名"],
            ["keywords", "require", "请输入页面关键词"],
            ["summary", "require", "请输入页面描述"],
            ["content", "require", '请输入活动详情'],
        ];
        $validate = new Validate($rule);
        $data = $request->post();
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), "failed");
        }
        $user = $this->getSessionUser();
        $data["node_id"] = $user["user_node_id"];
        //本地图片位置
        $type = $this->analyseUrlFileType($data['oss_img_src']);
        //生成随机的文件名
        $data['img_name'] = $this->formUniqueString() . ".{$type}";
        if (!creative::update($data, ["id" => $id])) {
            return $this->resultArray("修改失败", "failed");
        }
        $this->open_start('正在修改中');
        $where = [];
        $where['node_id'] = $user["user_node_id"];
        $site = (new \app\admin\model\Site())->where($where)->select();
        foreach ($site as $k => $v) {
            if ($v['sync_id']) {
                //print_r($v['url'] . '/regenerateactivity/'.$data['id']);
                $this->curl_get($v['url'] . '/regenerateactivity/' . $data['id']);
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
     * 上传活动缩略图
     * @return array
     */
    public function imageUpload()
    {
        $data = $this->uploadImg("activity/");
        if ($data['status']) {
            $data["msg"] = "上传成功";
            return $data;
        } else {
            return $this->resultArray('上传失败', 'failed');
        }

    }

    /**
     * 外站添加
     * @param Request $request
     * @return array
     */
    public function storyOut(Request $request)
    {
        $rule = [
            ["title", "require", "请输入标题"],
            ["oss_img_src", "require", "请先上传图片"],
            ["url", "require", "请输入外部链接"]
        ];
        $validate = new Validate($rule);
        $data = $request->post();
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError());
        }
        $user = $this->getSessionUser();
        $data["node_id"] = $user["user_node_id"];
        //本地图片位置
        $type = $this->analyseUrlFileType($data['oss_img_src']);
        //生成随机的文件名
        $data['img_name'] = $this->formUniqueString() . ".{$type}";
        if (creative::create($data)) {
            return $this->resultArray("添加成功");
        }
        return $this->resultArray("添加失败", "failed");
    }

    /**
     * 修改外部活动
     * @param $id
     * @return array
     */
    public function saveOut($id)
    {
        $rule = [
            ["title", "require", "请输入标题"],
            ["oss_img_src", "require", "请先上传图片"],
            ["url", "require", "请输入外部链接"]
        ];
        $request = Request::instance();
        $validate = new Validate($rule);
        $data = $request->post();
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), "failed");
        }
        if (creative::update($data, ["id" => $id])) {
            return $this->resultArray("修改成功");
        }
        return $this->resultArray("修改失败", "failed");
    }


    /**
     * 获取产品多图状态下的图片src
     * @access public
     */
    public function getImgSer($id)
    {
        $data = (new creative)->where(["id" => $id])->field("id,imgser")->find()->toArray();
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
     * 删除图片中个别的imgser
     * @access public
     */
    public function deleteImgser($id, $index)
    {
        $data = (new creative)->where(["id" => $id])->field("id,imgser")->find();
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
        return $this->resultArray('删除图片完成', '', $imglist);
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
        $dest_dir = 'activity/imgser/';
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
                $img_name = $this - $this->formUniqueString();
            }
            $data = (new creative)->where(["id" => $id])->field("id,imgser")->find();
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
     * 修改状态
     * @param $id
     * @param $status
     * @return array
     */
    public function changeStatus($id, $status)
    {
        if (creative::where(["id" => $id])->update(["status" => $status])) {
            return $this->resultArray("修改成功");
        }
        return $this->resultArray("修改失败", "failed");
    }

}
