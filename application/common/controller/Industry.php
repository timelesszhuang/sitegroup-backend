<?php
/**
 * Created by PhpStorm.
 * User: jingzheng
 * Date: 2017/4/21
 * Time: 11:35
 */
namespace app\common\controller;
use think\Validate;

class Industry extends Common{
    /**
     * 查询数据
     * @param $id
     * @return array|false|\PDOStatement|string|\think\Model
     * @auther jingzheng
     */
    public function index($id)
    {
        if ($this->request->isGet())
        {
            $Industry = new \app\common\model\Industry();
            return $Industry->field("id,name,create_time,update_time")->where(["id" => $id])->find();
        }
    }

    /**
     * 分页数据
     * @return array
     * @auther jingzheng
     */
    public function read(){
        if ($this->request->isGet())
        {
            $request = $this->getLimit();
            return  (new \app\common\model\Industry())->getIndustry($request['limit'], $request["rows"]);
        }
    }
    /**
     * 添加数据
     * @return array
     * @auther jingzheng
     */
    public function add(){
        if($this->request->isPost()){
            $rule = [
                ["name", "require", "请输入行业名"],
                ['detail', 'require', '详细必须'],
            ];
            $data = $this->request->post();
            $validate = new Validate($rule);
            if (!$validate->check($data)) {
                return $this->resultArray($validate->getError(), 'failed');
            }
            if (!\app\common\model\Industry::create($data)) {
                return $this->resultArray('添加失败', 'failed');
            }
            return $this->resultArray('添加成功');
        }
    }
    /**
     * 修改数据
     * @return array
     * @auther jingzheng
     */
    public function update()
    {
        if ($this->request->isPut()) {
            $rule = [
                ["name", "require", "请输入行业名"],
                ['detail', 'require', '详细必须'],
            ];
            $data = $this->request->post();
            $validate = new Validate($rule);
            if (!$validate->check($data)) {
                return $this->resultArray($validate->getError(), 'failed');
            }
            if (!\app\common\model\Industry::create($data)) {
                return $this->resultArray('修改失败', 'failed');
            }
            return $this->resultArray();
        }
    }


    /**
     * 删除
     * @param  int $id
     * @return \think\Response
     * auther jingzheng
     */
    public function delete($id)
    {
        if ($this->request->isDelete()) {
            $Industry = \app\common\model\Industry::get($id);
            if (!$Industry->delete()) {
                return $this->resultArray('删除失败', 'failed');
            }
            return $this->resultArray('删除成功');

        }
    }

}

