<?php
/**
 * Created by PhpStorm.
 * User: jingzheng
 * Date: 2017/4/21
 * Time: 11:35
 */
namespace app\sysadmin\controller;
use app\common\controller\Common;
use think\Validate;
use think\Request;

class Industry extends Common {
    /**
     * 查询数据
     * @param $id
     * @return array|false|\PDOStatement|string|\think\Model
     * @author jingzheng
     */
    public function read($id)
    {
        return $this->resultArray('','',\app\sysadmin\model\Industry::get($id));

    }
    public function create(){

    }
    /**
     * 分页数据
     * @return array
     * @author jingzheng
     */
    public function index(){

        $request=$this->getLimit();
        $name = $this->request->get('name');
        $id = $this->request->get('id');
        $where = [];
        if(!empty($name)){
            $where["name"]=["like","%$name%"];
        }
        if(!empty($id)){
            $where["id"]=$id;
        }
        return $this->resultArray('','',(new \app\sysadmin\model\Industry())->getIndustry($request["limit"],$request["rows"],$where));

    }
    /**
     * 添加数据
     * @return array
     * @author jingzheng
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
        if (!\app\sysadmin\model\Industry::create($data)) {
            return $this->resultArray('添加失败', 'failed');
        }
        return $this->resultArray('添加成功');
    }



    /**
     * 修改数据
     * @return array
     * @author jingzheng
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
        if (!\app\sysadmin\model\Industry::update($data)) {
            return $this->resultArray('修改失败', 'failed');
        }
        return $this->resultArray('修改成功');

    }


    /**
     * 删除
     * @param  int $id
     * @return \think\Response
     * @author jingzheng
     */
    public function delete($id)
    {
        $Industry = \app\sysadmin\model\Industry::get($id);
        if (!$Industry->delete()) {
            return $this->resultArray('删除失败', 'failed');
        }
        return $this->resultArray('删除成功');

    }

    /**
     * 获取行业id name
     * @return array
     * @author jingzheng
     */

    public function getIndustry(){
        return $this->resultArray('','',(new \app\sysadmin\model\Industry())->getSort());
    }
}

