<?php
/**
 * Created by IntelliJ IDEA.
 * User: qiangbi
 * Date: 1/29/18
 * Time: 3:50 PM
 */

namespace app\common\controller;


use app\common\exception\ProcessException;
use app\common\traits\Osstrait;
use think\Request;

class OssUpload extends CommonLogin
{
    use Osstrait;

    /**
     * 图片上传到 oss相关操作
     * @access public
     * @param string $dir_name 目录名
     * @return array
     */
    public function imageUpload($dir_name)
    {
        try {
            $url = $this->uploadImg($dir_name . '/');
            return $this->resultArray(['url' => $url], '上传成功');
        } catch (ProcessException $exception) {
            return $this->resultArray('failed', '上传失败');
        }
    }

    /**
     * csv上传到 oss相关操作
     * @access public
     * @param $dir_name
     * @return array
     */
    public function csvUpload($dir_name)
    {
        try {
            $request = Request::instance();
            $file = $request->file('file');
            $localpath = ROOT_PATH . "public/upload/";
            $fileInfo = $file->move($localpath);
            $localfilepath = $localpath . $fileInfo->getSaveName();
            $url = $this->uploadObj($dir_name . '/' . $fileInfo->getSaveName(), $localfilepath);
            return $this->resultArray(['url' => $url], '上传成功');
        } catch (ProcessException $exception) {
            return $this->resultArray('failed', '上传失败');
        }
    }
}