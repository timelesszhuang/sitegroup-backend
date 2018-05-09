<?php
// +----------------------------------------------------------------------
// | Description: 用户
// +----------------------------------------------------------------------
// | Author: linchuangbin <linchuangbin@honraytech.com>
// +----------------------------------------------------------------------

namespace app\common\controller;


use app\common\exception\ProcessException;
use app\common\model\District;
use app\common\traits\Obtrait;
use app\common\model\Childsitelist as this_model;

class Childsitelist extends CommonLogin
{
    use Obtrait;

    public function __construct()
    {
        parent::__construct();
        $this->model = new this_model();
    }

    public function index()
    {
        $request = $this->getLimit();
        $site_id = $this->request->get('site_id');
        $where = [];
        $user_info = $this->getSessionUserInfo();
        $where["node_id"] = $user_info["node_id"];
        $where["site_id"] = $site_id;
        $count = $this->model->where($where)->count();
        $data = $this->model->where($where)->order('id desc')->limit($request["limit"], $request["rows"])->select();
        $data = [
            "total" => $count,
            "rows" => $data
        ];
        return $this->resultArray($data);
    }

    public function setchildsitelist(){
        $site_id = $this->request->post('site_id');
        $area_id = $this->request->post('district_id');
        $level = $this->request->post('level');
        try{
            $this->set_childsitelist($site_id, $area_id, $level);
            return $this->resultArray('success', '添加成功');
        } catch (ProcessException $exception) {
            return $this->resultArray("failed", $exception->getMessage());
        }
    }

    public function set_childsitelist($site_id, $area_id, $level)
    {
        //站点信息
        $Childsitelist = new this_model();
        $District = new District();
        $field = 'id,name,pinyin,parent_id,suffix,level';
        $parent = $District->where(['id' => $area_id, "level" => ['<=', $level]])->field($field)->find();
        $sitelist = $District->where(["level" => ['<=', $level], 'path' => ['like', "%,{$area_id},%"]])->field($field)->select();
        if ($parent) {
            array_push($sitelist, $parent);
        }
        $add_data = [];
        $user_info = $this->getSessionUserInfo();
        foreach ($sitelist as $district) {
            $add_data[$district['pinyin']] = [
                'district_id' => $district['id'],
                'p_id' => $district['parent_id'],
                'path' => $district['path'],
                'en_name' => $district['pinyin'],
                'site_id' => $site_id,
                'name' => $district['name'],
                'detail' => $district['name'],
                'node_id' => $user_info['node_id'],
            ];
        }
        if (!(count($add_data) > 0)) {
            Common::processException('请正确选择需要添加的站点');
        }
        $old_childsitelist = $Childsitelist->where(['en_name' => ['in', array_keys($add_data)], 'site_id' => $site_id])->select();
        if ($old_childsitelist) {
            foreach ($old_childsitelist as $childsitelist) {
                unset($add_data[$childsitelist['en_name']]);
            }
        }
        if ((count($add_data) > 0)&&(!$Childsitelist->insertAll($add_data))) {
            Common::processException('添加失败');
        }
    }
}