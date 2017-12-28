<?php

namespace app\user\controller;

use app\admin\model\Menu;
use app\admin\model\QuestionType;
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
    public function index()
    {
        $content = $this->request->get('content');
        $type_id = $this->request->get("type_id");
        $request = $this->getLimit();
        $type_ids = $this->__getSiteQuestionTypeids();
        $aricle['type_id'] = ['in', $type_ids];
        if (!empty($content)) {
            $aricle['question'] = ["like", "%$content%"];
        }
        if (!empty($type_id)) {
            $aricle['type_id'] = $type_id;
        }
        //dump($aricle['type_id']);
        $articledata = (new \app\admin\model\Question())->where($aricle)->limit($request["limit"], $request["rows"])->order('id desc')->select();
        $count = (new \app\admin\model\Question())->where($aricle)->count();
        $data = [
            "total" => $count,
            "rows" => $articledata
        ];
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
     * 获取 站点对应的文章分类的typeid 数组
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function __getSiteQuestionTypeids()
    {
        $wh['id'] = $this->request->session()['website']['id'];
        $menu = $this->getSiteSession('website')['menu'];
        $pmenuids = array_filter(explode(',', $menu));
        $menuObj = new \app\admin\model\Menu();
        $menuObj = $menuObj->where('flag', 2)->where('id', 'in', $pmenuids);
        foreach ($pmenuids as $v) {
            $menuObj = $menuObj->whereOr('path', 'like', "%,$v,%");
        }
        $menus = $menuObj->select();
        $types = [];
        foreach ($menus as $v) {
            $types = array_merge($types, array_filter(explode(',', $v['type_id'])));
        }
        return $types;
    }

    /**
     *
     */
    public function getQuestionType()
    {
        $types = $this->__getSiteQuestionTypeids();
        $typearr = (new QuestionType())->alias('type')->join('type_tag', 'type_tag.id=type.tag_id', 'LEFT')->whereIn('type.id', $types)->field('type.id,name,tag')->select();
        $final = [];
        foreach ($typearr as $v) {
            if ($v['tag']) {
                $final[$v['tag']][] = [
                    'id' => $v['id'],
                    'name' => $v['name']
                ];
            } else {
                $final['未定义'][] = [
                    'id' => $v['id'],
                    'name' => $v['name']
                ];
            }
        }
        return $this->resultArray('', '', $final);
    }

    /**
     * @return array
     * 问答预览
     */

    public function questionshowhtml()
    {
        $id = $this->request->post('id');
        $url =  $this->getSiteSession('website')['url'];
        $showurl = $url . '/preview/question/' . $id . '.html';
        return $this->resultArray('', '', $showurl);

    }
}
