<?php

namespace app\sysadmin\controller;

use app\common\controller\Common;
use think\Request;
use app\common\model\HtmlTemplate as Html;
use think\Validate;

class HtmlTemplate extends Common
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {

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
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $rule = [
            ["img", "require", "请上传图片"],
            ['path', 'require', '请上传模板'],
            ['holiday_id',"require","请上传id"],
            ['holiday_name',"require","请上传名称"]
        ];
        $data = $this->request->post();
        $validate = new Validate($rule);
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), 'failed');
        }
        if (!Html::create($data)) {
            return $this->resultArray('添加失败', 'failed');
        }
        return $this->resultArray('添加成功');
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }

    /**
     * 上传html5模板
     * @return array
     */
    public function uploadTemplate()
    {
        $file = request()->file('file_name');
        $info = $file->move(ROOT_PATH . 'public/upload/htmlTemplate');
        if ($info) {
            // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
            return $this->resultArray('上传成功', '', "upload/htmlTemplate/".$info->getSaveName());
        } else {
            // 上传失败获取错误信息
            return $this->resultArray('上传失败', 'failed', $info->getError());
        }
    }

    /**
     * 上传html5模板缩略图
     * @return array
     */
    public function uploadTemplateImg()
    {
        $file = request()->file('img_name');
        $info = $file->move(ROOT_PATH . 'public/upload/htmlTemplateImg');
        if ($info) {
            // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
            return $this->resultArray('上传成功', '', "upload/htmlTemplateImg/".$info->getSaveName());
        } else {
            // 上传失败获取错误信息
            return $this->resultArray('上传失败', 'failed', $info->getError());
        }
    }
}
