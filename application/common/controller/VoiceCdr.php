<?php
namespace app\common\controller;


use think\Request;
use app\common\model\VoiceCdr as model;

class VoiceCdr extends Common
{
    /**
     * 显示资源列表
     *
     * @param Request $request
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author guozhen
     */
    public function index(Request $request)
    {

        $limits = $this->getLimit();
        $where = [];
        $user_info = $this->getSessionUserInfo();
        if ($user_info['user_type_name'] == 'node') {
            $where["node_id"] = $user_info["node_id"];
        }
        $param = $this->request->param();
        if(isset($param['node_id'])&&$param['node_id']){
            $where['node_id']=$param['node_id'];
        }
        $data=(new model())->getAll($limits['limit'], $limits['rows'], $where);
        foreach($data['rows'] as $key=>$datas){
            $data['rows'][$key]['timestart']=date('Y-m-d H:i:s',$datas['timestart']);
            $data['rows'][$key]['timeend']=date('Y-m-d H:i:s',$datas['timeend']);
            $data['rows'][$key]['rec_name']=$datas['rec_name']?'http://voice.salesman.cc/lexiaoyi/'.$datas['rec_name'].'.mp3':'';
        }
        return $this->resultArray('', '',$data);
    }


}
