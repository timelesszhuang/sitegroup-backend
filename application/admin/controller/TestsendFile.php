<?php

namespace app\admin\controller;

use app\common\controller\Common;
use think\Request;
use think\Validate;

/**
 * 该文件的作用是测试上传文件
 * @author xingzhuang
 * 2017年5月17
 */
class TestsendFile extends Common
{

    //该目录是相对于 public  使用 ROOT_PATH 需 手动追加 public/ 目录
    static $demopath = 'upload/demo';

    /**
     * 测试上传文件相关操作
     * @return array
     */
    public function index()
    {
        $file = request()->file('file');
        $info = $file->move(ROOT_PATH . 'public/' . self::$demopath);
        $file_savename = $info->getSaveName();
        $pathinfo = pathinfo($file_savename);
        if ($info) {
            return $this->resultArray('上传成功', '', $file_savename);
        } else {
            // 上传失败获取错误信息
            return $this->resultArray('上传失败', 'failed', $info->getError());
        }
    }


}