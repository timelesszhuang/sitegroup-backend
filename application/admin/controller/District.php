<?php

namespace app\admin\controller;


use app\common\controller\Common;
use app\common\controller\CommonLogin;
use app\common\exception\ProcessException;
use think\Request;
use think\Validate;
use app\common\model\District as this_model;

class District extends Common
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
        $data = $this->model->getdistrict($id);
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



}