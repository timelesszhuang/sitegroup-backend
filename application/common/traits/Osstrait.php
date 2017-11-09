<?php
/**
 * Created by PhpStorm.
 * oss 相关操作封装
 * User: 赵兴壮
 * Date: 17-6-12
 * Time: 上午9:44
 */

namespace app\common\traits;

use OSS\OssClient;
use think\Config;
use think\Request;
trait Osstrait
{

    /**
     * url 安全的base64 编码
     * @access private
     */
    private function urlsafe_b64encode($string)
    {
        $data = base64_encode($string);
        $data = str_replace(array('+', '/', '='), array('-', '_', ''), $data);
        return $data;
    }

    /**
     * oss 对象上传
     * @param $object 服务器上文件名
     * @param $filepath 本地文件的绝对路径 比如/home/wwwroot/***.jpg
     */
    public function ossPutObject($object, $filepath)
    {
        $accessKeyId = Config::get('oss.accessKeyId');
        $accessKeySecret = Config::get("oss.accessKeySecret");
        $endpoint = Config::get('oss.endpoint');
        $bucket = Config::get('oss.bucket');
        $status = true;
        try {
            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
            $ossClient->uploadFile($bucket, $object, $filepath);
            $msg = '上传成功';
        } catch (OssException $e) {
            $msg = $e->getMessage();
            $status = false;
        }
        return ['status' => $status, 'msg' => $msg];
    }


    /**
     * oss 获取对象
     * @access public
     */
    public function ossGetObject($object, $filepath)
    {
        $accessKeyId = Config::get('oss.accessKeyId');
        $accessKeySecret = Config::get("oss.accessKeySecret");
        $endpoint = Config::get('oss.endpoint');
        $bucket = Config::get('oss.bucket');
        $status = true;
        $options = array(
            OssClient::OSS_FILE_DOWNLOAD => $filepath,
        );
        try {
            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
            $ossClient->getObject($bucket, $object, $options);
            $msg = '获取成功';
        } catch (OssException $e) {
            $msg = $e->getMessage();
            $status = false;
        }
        return ['status' => $status, 'msg' => $msg];
    }

    /**
     * 删除 oss 中的对象
     * @access public
     * @param $object 要删除的对象  支持带着绝对路径
     * @return array
     */
    public function ossDeleteObject($object)
    {
        $accessKeyId = Config::get('oss.accessKeyId');
        $accessKeySecret = Config::get("oss.accessKeySecret");
        $endpoint = Config::get('oss.endpoint');
        $bucket = Config::get('oss.bucket');
        //如果路径里边包含绝对https 之类路径则替换掉 https://***/
        $object = str_replace(sprintf("https://%s.%s/", $bucket, $endpoint), '', $object);
        $status = true;
        try {
            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
            $ossClient->deleteObject($bucket, $object);
            $msg = '删除成功';
        } catch (OssException $e) {
            $status = false;
            $msg = $e->getMessage();
        }
        return ['status' => $status, 'msg' => $msg];
    }

    /**
     * 处理oss 相关操作
     * @access public
     */
    public function ossCreateObject()
    {
        $accessKeyId = Config::get('oss.accessKeyId');
        $accessKeySecret = Config::get("oss.accessKeySecret");
        $endpoint = Config::get('oss.endpoint');
        $bucket = Config::get('oss.bucket');
        $status = true;
        try {
            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
            //oss 权限为公共写权限
            $ossClient->createBucket($bucket, OssClient::OSS_ACL_TYPE_PUBLIC_READ);
            $msg = '创建bucket成功';
        } catch (OssException $e) {
            $status = false;
            $msg = $e->getMessage();
        }
        return ['status' => $status, 'msg' => $msg];

    }

    /**
     * oss图片上传
     * @param $dest_dir
     * @param string $uname
     * @return array
     */
    public function uploadImg($dest_dir,$uname="file")
    {
        $endpoint = Config::get('oss.endpoint');
        $bucket = Config::get('oss.bucket');
        $request = Request::instance();
        $file = $request->file($uname);
        $localpath = ROOT_PATH . "public/upload/";
        $fileInfo = $file->move($localpath);
        $object = $dest_dir . $fileInfo->getSaveName();
        $localfilepath = $localpath . $fileInfo->getSaveName();
        $put_info = $this->ossPutObject($object, $localfilepath);
        unlink($localfilepath);
        $url = '';
        $status = false;
        if ($put_info['status']) {
            $status = true;
            $url = sprintf("https://%s.%s/%s", $bucket, $endpoint, $object);
        }
        return [
            "url" => $url,
            'status' => $status
        ];
    }




}