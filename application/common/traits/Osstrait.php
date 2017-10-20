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
     * @param $bucket 桶的名称 比如 product 比如 article 比如 template等
     */
    public function ossPutObject($object, $filepath, $bucket)
    {
        $accessKeyId = Config::get('oss.accessKeyId');
        $accessKeySecret = Config::get("oss.accessKeySecret");
        $endpoint = Config::get('oss.endpoint');
        $status = 'success';
        try {
            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
            $ossClient->uploadFile($bucket, $object, $filepath);
            $msg = '上传成功';
        } catch (OssException $e) {
            $msg = $e->getMessage();
            $status = 'failed';
        }
        return ['status' => $status, 'msg' => $msg];
    }


    /**
     * oss 获取对象
     * @access public
     */
    public function ossGetObject($object, $filepath, $bucket)
    {
        $accessKeyId = Config::get('oss.accessKeyId');
        $accessKeySecret = Config::get("oss.accessKeySecret");
        $endpoint = Config::get('oss.endpoint');
        $status = 'success';
        $filepath = "141414.php";
        $options = array(
            OssClient::OSS_FILE_DOWNLOAD => $filepath,
        );
        try {
            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
            $ossClient->getObject($bucket, $object, $options);
            $msg = '获取成功';
        } catch (OssException $e) {
            $msg = $e->getMessage();
            $status = 'failed';
        }
        return ['status' => $status, 'msg' => $msg];
    }

    /**
     * 删除 oss 中的对象
     * @access public
     * @param $object 要删除的对象
     * @param $bucket 桶
     */
    public function ossDeleteObject($object, $bucket)
    {
        $accessKeyId = Config::get('oss.accessKeyId');
        $accessKeySecret = Config::get("oss.accessKeySecret");
        $endpoint = Config::get('oss.endpoint');
        //$object = "oss-php-sdk-test/upload-test-object-name.txt";
        $status = 'success';
        try {
            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
            $ossClient->deleteObject($bucket, $object);
            $msg = '获取成功';
        } catch (OssException $e) {
            printf($e->getMessage() . "\n");
        }
        print(__FUNCTION__ . ": OK" . "\n");
    }


    public function ossdemo()
    {
        $accessKeyId = Config::get('oss.accessKeyId');
        $accessKeySecret = Config::get("oss.accessKeySecret");
        $endpoint = Config::get('oss.endpoint');

        $bucket = "salesman1";
        $object = "141414.jpg";
        $filePath = __FILE__;
        //图片加水印
        $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
        $download_file = 'demo.jpg';
        $water = '山东强比信息技术有限公司';
        $code = $this->urlsafe_b64encode($water);
        $options = array(
            OssClient::OSS_FILE_DOWNLOAD => $download_file,
            OssClient::OSS_PROCESS => "image/watermark,text_{$code},color_FFFFFF");
        $ossClient->getObject($bucket, $object, $options);


        //图片上传
        try {
            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
            $ossClient->uploadFile($bucket, $object, $filePath);
        } catch (OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
        print(__FUNCTION__ . ": OK" . "\n");


//        exit;


//        创建资源包
        try {
            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
            $ossClient->createBucket($bucket, OssClient::OSS_ACL_TYPE_PRIVATE);
        } catch (OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
        print(__FUNCTION__ . ": OK" . "\n");
        EXIT;


//      下载资源
        $object = "141414.jpg";
        $localfile = "141414.php";
        $options = array(
            OssClient::OSS_FILE_DOWNLOAD => $localfile,
        );
        try {
            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
            $ossClient->getObject($bucket, $object, $options);
        } catch (OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
        print(__FUNCTION__ . ": OK, please check localfile: 'upload-test-object-name.txt'" . "\n");

    }
}