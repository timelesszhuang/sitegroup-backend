<?php

namespace app\admin\controller;


use app\common\controller\Common;
use app\common\controller\CommonLogin;
use app\common\exception\ProcessException;
use think\Request;
use think\Validate;
use app\common\model\Keyword as this_model;

class Keyword extends CommonLogin
{

    public function __construct()
    {
        parent::__construct();
        $this->model = new this_model();
    }

    /**
     * 显示资源列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author jingzheng
     */
    public function index()
    {
        $id = $this->request->get('id');
        if (empty($id)) {
            $id = 0;
        }
        $data = $this->model->getKeyword($id);
        return $this->resultArray($data);
    }

    /**
     * 显示指定的资源
     *
     * @param  int $id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function read($id)
    {
        return $this->getread($this->model, $id);
    }

    /**
     * 删除指定资源
     * @param  int $id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function delete($id)
    {
        try {
            $keyword = $this->model;
            $user = $this->getSessionUserInfo();
            $where["parent_id"] = $id;
            $where["node_id"] = $user["node_id"];
            $key = $keyword->where($where)->select();
            if (!empty($key)) {
                Common::processException('此节点有子节点，无法删除');
            }
            if ($keyword->where(["id" => $id, "node_id" => $user["node_id"]])->delete() == false) {
                Common::processException('节点删除失败');
            }
            return $this->resultArray('删除成功');
        } catch (ProcessException $e) {
            return $this->resultArray('failed', $e->getMessage());
        }
    }

    /**
     * 上传关键词文件文件
     * @return array
     */
    public function uploadKeyword()
    {
        $file = request()->file('file_name');
        $info = $file->move(ROOT_PATH . 'public/upload');
        if ($info) {
            return $this->resultArray('上传成功', '', $info->getSaveName());
        } else {
            // 上传失败获取错误信息
            return $this->resultArray('上传失败', 'failed', $info->getError());
        }
    }

    /**
     * 根据上传的文件名 导入关键词
     * @param Request $request
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author guozhen
     */
    public function insertKeyword(Request $request)
    {
        $one_class_num = 20;
        $post = $request->post();
        $rule = [
            ["id", "require", "请传入id"],
            ["path", "require", "请传入path"]
        ];
        $validate = new Validate($rule);
        if (!$validate->check($post)) {
            return $this->resultArray($validate->getError(), 'failed');
        }
        $model = $this->model;
        $file_info = $this->getKeywordInfo($post["path"], $post["id"], $model);
        //如果是A 那么当前上传的就是B类
        $count = $this->model->where(["path" => ["like", "%," . $post['id'] . ",%"]])->count();
        if ($count > $one_class_num) {
            return $this->resultArray("当前分类下面关键词超过 $one_class_num 个", 'failed');
        }
        $num = $count;
        while ($key = fgets($file_info["file"])) {
            $num += 1;
            if ($num > $one_class_num) {
                return $this->resultArray("当前分类下面关键词不能超过 $one_class_num 个,新导入" . ($num - $count - 1) . "个关键词,满 $one_class_num 个", 'failed');
            }
            $key = str_replace(PHP_EOL, '', trim($key));
            if (empty($key)) {
                continue;
            }
            $getkey = $model->where(["name" => $key, "parent_id" => $post["id"]])->find();
            if (!empty($getkey)) {
                continue;
            }
            $this->model->create([
                "name" => $key,
                "parent_id" => $post["id"],
                "path" => $file_info["path"],
                "tag" => $file_info["tag"],
                "node_id" => $file_info["user_node_id"]
            ]);
        }
        return $this->resultArray("添加成功");
    }

    public function getKeywordByFile()
    {
        try {
            $file = request()->file('file');
            if (!$file) {
                Common::processException('请上传文件');
            }
            $datas = [];
            //编码格式
            $encoding = "";
            while ($data = $file->fgetcsv()) {
                if ($encoding === "") {
                    $encoding = mb_detect_encoding($data[0], array("ASCII", 'UTF-8', "GB2312", "GBK", 'BIG5'));
                }
                if ($encoding !== "" && $encoding)
                    foreach ($data as $key => $value) {
                        $data[$key] = iconv($encoding, 'UTF-8//TRANSLIT//IGNORE', $value);
                    }
                $datas = array_merge($datas, $data);
            }
            $datas = array_filter($datas);
            return $this->resultArray($datas);
        } catch (ProcessException $e) {
            return $this->resultArray('failed', $e->getMessage());
        }

    }

    /**
     * 添加A类关键词
     * @return array
     * @throws \Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function save()
    {
        try {
            $id = $this->request->param('id', 0);
            $rule = [
                ["name", "require", "请填写关键词"],
            ];
            $validate = new Validate($rule);
            $data = $this->request->post();
            if (!$validate->check($data)) {
                Common::processException($validate->getError());
            }
            $keyArr = explode("\n", $data['name']);
            $user = $this->getSessionUserInfo();
            $node_id = $user["node_id"];
            if (empty($keyArr) || !is_array($keyArr)) {
                Common::processException('请提交关键词');
            }
            if ($id == 0) {
                $tag = 'A';
                $path = '';
            } else {
                $parent = $this->model->find(['id' => $id]);
                if ($parent['tag'] == 'C') {
                    Common::processException('此分类无法再添加子集关键词');
                }
                $path_array = array_filter(explode(',', $parent['path']));
                $path_array[] = $id;
                $path = "," . implode(',', $path_array) . ",";
                $tag = chr(ord($parent['tag']) + 1);
            }
            $save_array = [];
            $where_old_data = [];
            $where_old_data['node_id'] = $node_id;
            $where_old_data['parent_id'] = $id;
            $where_old_data['name'] = ['in', $keyArr];
            $old_data = $this->model->where(['node_id' => $node_id, 'parent_id' => $id])->select();
            $isset = [];
            foreach ($old_data as $old_item) {
                $isset[] = $old_item['name'];
            }
            foreach ($keyArr as $item) {
                if (empty(trim($item)) || in_array($item, $isset)) {
                    continue;
                }
                $save_array[] = [
                    "name" => $item,
                    "node_id" => $node_id,
                    "parent_id" => $id,
                    "path" => $path,
                    "tag" => $tag,
                ];
            }
            $add_array = $this->model->saveAll($save_array);
            foreach ($add_array as $key => $item) {
                $add_array[$key]['label'] = $add_array[$key]['name'];
                unset($add_array[$key]['create_time']);
                unset($add_array[$key]['update_time']);
                unset($add_array[$key]['path']);
                unset($add_array[$key]['parent_id']);
                unset($add_array[$key]['node_id']);
                unset($add_array[$key]['name']);
            }
            return $this->resultArray("添加成功!", $add_array);
        } catch (ProcessException $e) {
            return $this->resultArray('failed', $e->getMessage());
        }
    }


    /**
     * @return array
     * 关键词统计
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
//TODO oldfunction
    public function keywordCount()
    {
        $user = $this->getSessionUser();
        $where = [
            'node_id' => $user["user_node_id"],
        ];
        $keyword = $this->model;
        $arr = $keyword->field('tag,count(id) as tagCount')->where($where)->group('tag')->order("tagCount", "desc")->select();
        $te = [];
        foreach ($arr as $k => $v) {
            $te[] = $v['tagCount'];
            $ar[] = $v['tag'];
        }
        $temp = ["count" => $te, "name" => $ar];
        return $this->resultArray('', '', $temp);
    }

    /**
     * 删除所有关键词
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function deleteAll()
    {
        try {
            $rule = [
                ["id", "require", "请传入id"]
            ];
            $validate = new Validate($rule);
            $data = $this->request->post();
            if (!$validate->check($data)) {
                Common::processException($validate->getError());
            }
            $child_where = [];
            $user = $this->getSessionUserInfo();
            $child_where['parent_id'] = ['in', $data['id']];
            $child_where['node_id'] = $user["node_id"];
            $has_chinld = $this->model->where($child_where)->select();
            if ($has_chinld) {
                Common::processException('无法删除有子节点的节点');
            }
            $delect_where = [];
            $child_where['id'] = ['in', $data['id']];
            $child_where['node_id'] = $user["node_id"];
            if ($this->model->where($delect_where)->delete() == false) {
                Common::processException('节点删除失败');
            }
            return $this->resultArray('删除成功');
        } catch (ProcessException $e) {
            return $this->resultArray('failed', $e->getMessage());
        }
    }

    /**
     * 修改关键字名称
     * @param $id
     * @return array
     * @throws \think\exception\DbException
     */
    public function update($id)
    {
        try {
            $post = $this->request->post();
            $keyword = $this->model->get($id);
            $keyword->name = $post['name'];
            if (!$keyword->save()) {
                Common::processException('修改失败');
            }
            return $this->resultArray('修改成功');
        } catch (ProcessException $e) {
            return $this->resultArray('failed', $e->getMessage());
        }
    }

}