<?php

namespace app\admin\controller;

use app\common\controller\Common;
use app\common\traits\Osstrait;
use OSS\OssClient;
use think\Cache;
use think\Config;
use think\Db;
use think\Session;
use think\Validate;
use think\Request;
use app\common\traits\Obtrait;
use app\admin\model\Imglist as Img_list;

class ImgList extends Common
{
    use Obtrait;
    use Osstrait;

    /**
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $request = $this->getLimit();
        $where = [];
        $user = $this->getSessionUser();
        $where["node_id"] = $user["user_node_id"];
        $data = (new Img_list)->getImgList($request["limit"], $request["rows"], $where);
        return $this->resultArray('', '', $data);
    }

    /**
     * @param $id
     * @return array
     */
    public function read($id)
    {
        return $this->getread((new Img_list), $id);
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request $request
     * @return array
     */
    public function save(Request $request)
    {
        $rule = [
            ["name", "require", "请输入图集名称"],
            ["en_name", "require|alphaNum", "请输入图集英文名称|英文名格式只支持字母与数字"],
        ];
        $validate = new Validate($rule);
        $data = $request->post();
        $user = $this->getSessionUser();
        $data['node_id'] = $user['user_node_id'];
        $data['imgser'] = '';
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), "failed");
        }
        if (!Img_list::create($data)) {
            return $this->resultArray("添加失败", "failed");
        }
        return $this->resultArray("添加成功");
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
        $rule = [
            ["name", "require", "请输入图集名称"],
            ["en_name", "require|alphaNum", "请输入图集英文名称|英文名格式只支持字母与数字"],
        ];
        $data = $request->put();
        $validate = new Validate($rule);
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), 'failed');
        }
        if (!(new Img_list)->save($data, ["id" => $id])) {
            return $this->resultArray('修改失败', 'failed');
        }
        return $this->resultArray("修改成功");
    }

    /**
     * 删除指定资源
     * @param  int $id
     * @return array
     */
    public function changeStatus($id,$status)
    {
        $data['status']=$status;
        if (!(new Img_list)->save($data, ["id" => $id])) {
            return $this->resultArray('禁用失败', 'failed');
        }
        return $this->resultArray("禁用成功");
    }

    /**
     * 获取图集的图片src
     * @access public
     */
    public function getImgSer($id)
    {
        $data = (new Img_list)->where(["id" => $id])->field("id,imgser")->find()->toArray();
        $imgser = [];
        if ($data['imgser']) {
            $imgser = unserialize($data['imgser']);
            unset($data['imgser']);
        }
        $data['imglist'] = $imgser;
        return $this->resultArray('', '', $data);
    }

    /**
     * 修改 添加图片的Imgser 区分根据 $index 如果没有index是添加;
     * @access public
     */
    public function uploadImgSer()
    {
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
            $data = (new Img_list)->where(["id" => $id])->field("id,imgser")->find();
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
                $this->ossDeleteObject($deleteobject);
            }
        }
        $imglist = [];
        foreach ($imgser as $v) {
            $imglist[] = $v['osssrc'];
        }
        return [
            "imglist" => $imgser,
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
        $data = (new Img_list)->where(["id" => $id])->field("id,imgser")->find();
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
            $result = $this->ossDeleteObject($deleteobject);
        }
        return $this->resultArray('删除完成', '', $imgser);
    }
}
