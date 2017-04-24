<?php
/**
 * Created by PhpStorm.
 * User: jingzheng
 * Date: 2017/4/21
 * Time: 11:35
 */
namespace app\common\controller;
use think\Validate;
use think\Request;

class Industry extends Common{
    /**
     * 查询数据
     * @param $id
     * @return array|false|\PDOStatement|string|\think\Model
     * @auther jingzheng
     */
    public function read($id)
    {
        return $this->resultArray('','',\app\common\model\Industry::get($id));

    }
    public function create(){
        $request=$this->getLimit();
        return $this->resultArray('','',(new \app\common\model\Industry())->getSort($request));

    }
    /**
     * 分页数据
     * @return array
     * @auther jingzheng
     */
    public function index(){

        $request=$this->getLimit();
        return $this->resultArray('','',(new \app\common\model\Industry())->getIndustry($request["limit"],$request["rows"]));

    }
    /**
     * 添加数据
     * @return array
     * @auther jingzheng
     */
    public function save(Request $request){

        $rule = [
            ["name", "require|unique:Industry", "请输入行业名|行业名重复"],
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



    /**
     * 修改数据
     * @return array
     * @auther jingzheng
     */
    public function update(Request $request, $id)
    {
        $rule = [
            ["name", "require|unique:Industry", "请输入行业名|行业名重复"],
            ['detail', 'require', '详细必须'],
        ];
        $data = $this->request->put();
        $validate = new Validate($rule);
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), 'failed');
        }
        if (!\app\common\model\Industry::update($data)) {
            return $this->resultArray('修改失败', 'failed');
        }
        return $this->resultArray('修改成功');

    }


    /**
     * 删除
     * @param  int $id
     * @return \think\Response
     * auther jingzheng
     */
    public function delete($id)
    {
        $Industry = \app\common\model\Industry::get($id);
        if (!$Industry->delete()) {
            return $this->resultArray('删除失败', 'failed');
        }
        return $this->resultArray('删除成功');

    }

}

