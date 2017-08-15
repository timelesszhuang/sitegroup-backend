<?php

namespace app\admin\controller;

use app\common\controller\Common;
use think\Request;
use think\Validate;

class Keyword extends Common
{
    /**
     * 显示资源列表
     * @return \think\Response
     * @author jingzheng
     */
    public function index()
    {
        $tag = "";
        $id = $this->request->get('id');
        if (empty($id)) {
            $tag = "A";
        }
        $data = (new \app\admin\model\Keyword())->getKeyword($tag, $id);
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

    }

    /**
     * 显示指定的资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function read($id)
    {
        return $this->getread((new \app\admin\model\Keyword), $id);
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
        //
    }

    /**
     * 删除指定资源
     * @param  int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $keyword = new \app\admin\model\Keyword();
        $user = $this->getSessionUser();
        $where["parent_id"] = $id;
        $where["node_id"] = $user["user_node_id"];
        $key = $keyword->where($where)->select();
        if (!empty($key)) {
            return $this->resultArray('父级不能直接删除', 'failed');
        }
        if ($keyword->where(["id" => $id, "node_id" => $user["user_node_id"]])->delete() == false) {
            return $this->resultArray('父级节点不能删除', 'failed');
        }
        return $this->resultArray('删除成功');
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
            // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
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
     * @author guozhen
     */
    public function insertKeyword(Request $request)
    {
        $post = $request->post();
        $rule = [
            ["id", "require", "请传入id"],
            ["path", "require", "请传入path"]
        ];
        $validate = new Validate($rule);
        if (!$validate->check($post)) {
            return $this->resultArray($validate->getError(), 'failed');
        }
        $model = new \app\admin\model\Keyword();
        $file_info = $this->getKeywordInfo($post["path"], $post["id"], $model);
        while ($key = fgets($file_info["file"])) {
            $key = str_replace(PHP_EOL, '', trim($key));
            if(empty($key)){
                continue;
            }
            $getkey = $model->where(["name" => $key, "parent_id" => $post["id"]])->find();
            if (!empty($getkey)) {
                continue;
            }
            \app\admin\model\Keyword::create([
                "name" => $key,
                "parent_id" => $post["id"],
                "path" => $file_info["path"],
                "tag" => $file_info["tag"],
                "node_id" => $file_info["user_node_id"]
            ]);
        }
        return $this->resultArray("添加成功");
    }

    /**
     * 获取文件信息
     * @param $file_path
     * @param $id
     * @param $model
     * @return array
     * @author guozhen
     */
    public function getKeywordInfo($file_path, $id, $model)
    {
        $file_path = "upload/" . $file_path;
        if (file_exists($file_path)) {
            $oldKey = $model->where(["id" => $id])->find();
            $tag = "B";
            if ($oldKey["tag"] == "B") {
                $tag = "C";
            }
            $path = "," . $id . ",";
            if (!empty($oldKey["parent_id"])) {
                $path = "," . $oldKey["parent_id"] . "," . $id . ",";
            }
            $file = fopen($file_path, "r");
            $user = $this->getSessionUser();
            return [
                "tag" => $tag,
                "path" => $path,
                "file" => $file,
                "user_node_id" => $user["user_node_id"]
            ];
        }
        exit(json_encode($this->resultArray('文件不存在', "failed")));
    }

    /**
     * 添加A类关键词
     * @author guozhen
     * @return array
     */
    public function insertA()
    {
        $rule = [
            ["name", "require", "请填写A类关键词"],
        ];
        $validate = new Validate($rule);
        $data = $this->request->post();
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), 'faile');
        }
        $user = $this->getSessionUser();
        $data["node_id"] = $user["user_node_id"];
        $data['name'] = trim($data['name']);
        if (!\app\admin\model\Keyword::create($data)) {
            return $this->resultArray('添加失败', "faile");
        }
        return $this->resultArray('添加成功');
    }


    /**
     * @return array
     * 关键词统计
     */
    public function keywordCount()
    {
        $user = $this->getSessionUser();
        $where = [
            'node_id' => $user["user_node_id"],
        ];
        $keyword = new \app\admin\model\Keyword();
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
     * 插入A类或B类关键词
     * @return array
     */
    public function insertByTag()
    {
        $rule=[
            ["id","require","请传递id"],
            ["content","require","请提交关键词"]
        ];
        $request=Request::create();
        $data=$request->post();
        $validate=new Validate($rule);
        if(!$validate->check($data)){
            return $this->resultArray($validate->getError(),'faile');
        }
        $currentKey=\app\admin\model\Keyword::where(["id" => $data["id"]])->find();
        if(!isset($currentKey['tag'])){
            return $this->resultArray("当前关键词不存在",'faile');
        }

        switch($currentKey['tag']){
            case 'A':
                $parent_id=$data["id"];
                $path=",".$data["id"].",";
                $tag='B';
                break;
            case 'B':
                $parent_id=$data["id"];
                $path=$data["path"].$data["id"];
                $tag='C';
                break;
            default:
                return $this->resultArray("当前关键词非法",'faile');
        }

        //关键词数组
        $keyArr=explode('\n',$data['content']);
        $user = $this->getSessionUser();
        $node_id = $user["user_node_id"];
        if(empty($keyArr) || !is_array($keyArr)){
            return $this->resultArray("请提交关键词",'faile');
        }

        foreach($keyArr as $item){
            if(empty(trim($item))){
                continue;
            }
            \app\admin\model\Keyword::create([
                "name"=>$item,
                "parent_id"=>$parent_id,
                "path"=>$path,
                "node_id"=>$node_id,
                "tag"=>$tag,
            ]);
        }

    }




}