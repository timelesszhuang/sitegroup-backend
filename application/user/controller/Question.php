<?php

namespace app\user\controller;

use app\admin\model\Site;
use app\common\controller\Common;
use think\Controller;
use think\Request;
use app\common\traits\Obtrait;
use app\common\traits\Osstrait;
use think\Validate;

class Question extends Common
{
    use Obtrait;
    use Osstrait;

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index(Request $request)
    {
        $limits = $this->getLimit();
        $content = $request->get('content');
        $type_id = $request->get("type_id");
        $where = [];
        $node_id = $this->getSiteSession('login_site');
        $where["node_id"] = $node_id["node_id"];
        $where["site_id"] = $this->getSiteSession('website')["id"];
        if (!empty($content)) {
            $where['question'] = ["like", "%$content%"];
        }
        if (!empty($type_id)) {
            $where['type_id'] = $type_id;
        }
        $user = $this->getSessionUser();
        $site["node_id"] = $user["user_node_id"];
        $data = (new \app\admin\model\Question)->getAll($limits['limit'], $limits['rows'], $site);
        return $this->resultArray('','',$data);

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
            ['question', "require", "请填写问题"],
            ['content_paragraph', 'require', "请填写答案"],
            ["type_id", "require", "请选择分类id"],
            ["type_name", "require", "请选择分类名称"]
        ];
        $validate = new Validate($rule);
        $data = $this->request->post();
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), 'failed');
        }
        $data['node_id'] = $this->getSiteSession('login_site')["node_id"];
        $data["site_id"] = $this->getSiteSession('website')["id"];
        if (!\app\admin\model\Question::create($data)) {
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
        return $this->getread((new \app\admin\model\Question()), $id);
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
            ['question', "require", "请填写问题"],
            ['content_paragraph', 'require', "请填写答案"],
            ["type_id", "require", "请选择分类id"],
            ["type_name", "require", "请选择分类名称"]
        ];
        $validate = new Validate($rule);
        $data = $this->request->put();
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), 'failed');
        }
        $this->publicUpdate((new \app\admin\model\Question), $data, $id);
        $this->open_start('正在修改中');
        $where["id"] = $this->getSiteSession('website')["id"];
        // dump($where);die;
        $Site = (new Site())->where($where)->field('url')->find();
        $showurl = $Site['url'] . '/preview/question/' . $id . '.html';
        //dump($Site['url']);die;
        $send = [
            "id" => $data['id'],
            "searchType" => 'question',
            "type" => $data['type_id']
        ];
        $this->curl_post($showurl . "/index.php/generateHtml", $send);

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
     * @return array
     * 问答预览
     */

    public function questionshowhtml()
    {
        $id = $this->request->post('id');
        //$where['node_id'] = $this->getSiteSession('login_site')["node_id"];
        $where["id"] = $this->getSiteSession('website')["id"];
        // dump($where);die;
        $Site = (new Site())->where($where)->field('url')->find();
        $showurl = $Site['url'] . '/preview/question/' . $id . '.html';
        //dump($Site['url']);die;
        return $this->resultArray('', '', $showurl);

    }
}
