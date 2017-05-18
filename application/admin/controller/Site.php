<?php

namespace app\admin\controller;

use app\common\controller\Common;
use think\Request;
use think\Validate;

/**
 * 站点 最小的节点相关操作
 * @author xingzhuang
 * 2017年5月17
 */
class Site extends Common
{
    /**
     * 显示资源列表
     * @return \think\Response
     * @author xingzhuang
     */
    public function index()
    {
        $request = $this->getLimit();
        $site_name = $this->request->get('site_name');
        $where = [];
        if (!empty($site_name)) {
            $where["site_name"] = ["like", "%$site_name%"];
        }
        $user = (new Common())->getSessionUser();
        $where["node_id"] = $user["user_node_id"];
        $data = (new \app\admin\model\Site())->getAll($request["limit"], $request["rows"], $where);
        return $this->resultArray('', '', $data);
    }

    /**
     * 显示创建资源表单页.
     * @return \think\Response
     * @author xingzhuang
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
            ['site_name','require','请填写网站名称'],
            ['menu', 'require', "请选择菜单"],
            ['template_id','require','请选择模板'],
            ['ec','required','请填写ec代码'],
            ['support_hotline','required','请填写电话号码'],
            ['domain_id','required','请选择域名'],
            ['domain','required','请选择域名'],
            ['site_type','required','请选择网站类型'],
        ];
        $validate = new Validate($rule);
        $data = $this->request->post();
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), 'failed');
        }
        $data["node_id"] = $this->getSessionUser()['user_node_id'];
        if (!\app\admin\model\Site::create($data)) {
            return $this->resultArray('添加失败', 'failed');
        }
        return $this->resultArray('添加成功');
    }

    /**
     * 显示指定的资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function read($id)
    {
        return $this->getread((new \app\admin\model\Site), $id);
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
            ['site_name','require','请填写网站名称'],
            ['menu', 'require', "请选择菜单"],
            ['template_id','require','请选择模板'],
            ['ec','required','请填写ec代码'],
            ['support_hotline','required','请填写电话号码'],
            ['domain_id','required','请选择域名'],
            ['domain','required','请选择域名']
        ];
        $validate = new Validate($rule);
        $data = $this->request->put();
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), 'failed');
        }
        return $this->publicUpdate((new \app\admin\model\Site()), $data, $id);
    }

    /**
     * 删除指定资源 模板暂时不支持删除操作
     * @param  int $id
     * @return \think\Response
     */
    public function delete($id)
    {

    }


    /**
     * 传输模板文件到站点服务器
     * @access public
     */
    public function uploadTemplateFile()
    {
        $dest = 'http://local.sitegroup.com/index.php/testsendFile/index';
        $this->sendFile(ROOT_PATH . 'public/upload/20170427/1.csv', $dest, 'template');
    }
}