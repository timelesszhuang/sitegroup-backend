<?php

namespace app\admin\controller;

use think\Config;
use think\Request;
use app\common\controller\Common;
use think\Validate;
use app\common\traits\Obtrait;
use app\common\traits\Osstrait;

class Question extends Common
{
    use Obtrait;
    use Osstrait;

    /**
     * 显示资源列表
     *
     * @return \think\Response
     * @author guozhen
     */
    public function index(Request $request)
    {
        $limits = $this->getLimit();
        $content = $request->get('content');
        $type_id = $request->get("type_id");
        $where = [];
        if (!empty($content)) {
            $where['question'] = ["like", "%$content%"];
        }
        if (!empty($type_id)) {
            $where['type_id'] = $type_id;
        }
        $user = $this->getSessionUser();
        $where["node_id"] = $user["user_node_id"];
        return $this->resultArray('', '', (new \app\admin\model\Question)->getAll($limits['limit'], $limits['rows'], $where));
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     *
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
     * @author guozhen
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
        if(isset($data['tag_id'])&&is_array($data['tag_id'])){
            $data['tags']=','.implode(',',$data['tag_id']).',';
        }else{
            $data['tags']="";
        }
        unset($data['tag_id']);
        $data["node_id"] = $this->getSessionUser()['user_node_id'];
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
     * @author guozhen
     */
    public function read($id)
    {
        $data = $this->getread((new \app\admin\model\Question), $id);
        $data['data']['tags'] = implode(',',array_filter(explode(',',$data['data']['tags'])));
        return $data;
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
     * @author guozhen
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
        if(isset($data['tag_id'])&&is_array($data['tag_id'])){
            $data['tags']=','.implode(',',$data['tag_id']).',';
        }else{
            $data['tags']="";
        }
        unset($data['tag_id']);
        $this->publicUpdate((new \app\admin\model\Question), $data, $id);
        $this->open_start('正在修改中');
        $sitedata = $this->getQuestionSite($data['type_id']);
        if (array_key_exists('status', $sitedata)) {
            return $sitedata;
        }
        foreach ($sitedata as $kk => $vv) {
            $send = [
                "id" => $data['id'],
                "searchType" => 'question',
                "type" => $data['type_id']
            ];
            $this->curl_post($vv['url'] . "/index.php/generateHtml", $send);
        }
    }

    /**
     * 删除指定资源
     *
     * @param  int $id
     * @return \think\Response
     * @author guozhen
     */
    public function delete($id)
    {
        return $this->deleteRecord((new \app\admin\model\Question), $id);
    }

    /**
     * 图片上传到 oss相关操作
     * @access public
     */
    public function imageupload()
    {
        $dest_dir = 'question/';
        $endpoint = Config::get('oss.endpoint');
        $bucket = Config::get('oss.bucket');

        $request = Request::instance();
        $file = $request->file("file");
        $localpath = ROOT_PATH . "public/upload/";
        $fileInfo = $file->move($localpath);
        $object = $dest_dir . $fileInfo->getSaveName();
        $put_info = $this->ossPutObject($object, $localpath . $fileInfo->getSaveName());
        $msg = '上传缩略图失败';
        $url = '';
        $status = false;
        if ($put_info['status']) {
            $msg = '上传缩略图成功';
            $status = true;
            $url = sprintf("https://%s.%s/%s", $bucket, $endpoint, $object);
        }
        return [
            'msg' => $msg,
            "url" => $url,
            'status' => $status
        ];
    }

    /**
     * 获取 问题对应的站点
     * @param $type_id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function getQuestionSite($type_id)
    {
        //首先取出选择该文章分类的菜单 注意有可能是子菜单
        $where['flag'] = 2;
        $model_menu = (new \app\admin\model\Menu());
        $menu = $model_menu->where(function($query)use($where){
            $query->where($where);
        })->where(function($query)use($type_id){
            $query->where('type_id',['=',$type_id],['like',"%,$type_id,%"],'or');
        })->select();
        if (!$menu) {
            return $this->resultArray('问答分类没有菜单选中页面，暂时不能预览。', 'failed');
        }
        //一个菜单有可能被多个站点选择 site表中只会存储第一级别的菜单 需要找出当前的pid=0的父级菜单
        $pid = [];
        foreach ($menu as $k => $v) {
            $path = $v['path'];
            //该菜单的跟
            if ($path) {
                //获取第一级别的菜单
                $pid[] = array_values(array_filter(explode(',', $path)))[0];
            } else {
                $pid[] = $v['id'];
            }
        }
        $pid = array_unique($pid);
        //查询选择当前菜单的站点相关信息
        $map = '';
        foreach ($pid as $k => $v) {
            $permap = " menu like ',%$v%,' ";
            if ($k == 0) {
                $map = $permap;
                continue;
            }
            $map .= ' or ' . $permap;
        }
        $sitedata = \app\admin\model\Site::where($map)->field('id,site_name,url')->select();
        return $sitedata;
    }


    /***
     * 问答相关预览
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function questionshowhtml()
    {
        $data = $this->request->post();
        $sitedata = $this->getQuestionSite($data['type_id']);
        if (array_key_exists('status', $sitedata)) {
            return $sitedata;
        }
        foreach ($sitedata as $kk => $vv) {
            $showhtml[] = [
                'url' => $vv['url'] . '/preview/question/' . $data['id'] . '.html',
                'site_name' => $vv['site_name'],
            ];
        }
        if (!empty($showhtml)) {
            return $this->resultArray('', '', $showhtml);
        } else {
            return $this->resultArray('当前文章对应的菜单页面没有站点选择，暂时不能预览。', 'failed');
        }


    }
}
