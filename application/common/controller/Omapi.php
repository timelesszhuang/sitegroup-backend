<?php

namespace app\common\controller;

use think\Controller;
use think\Model;
use app\sysadmin\model\Node;
use app\common\model\VoiceCdr;
use think\Request;

class Omapi extends Controller
{
    /**
     * 显示资源列表
     * @return void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $xmldata = file_get_contents('php://input');
        $this->analyse_data($xmldata);
    }

    /**
     * 解析获取到的xml数据
     * @access private
     * @param xml数据 $xmldata xml数据  cdr 话单 /answered信息  answer信息
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function analyse_data($xmldata)
    {
        //xml 转换为对象
        $xml_obj = simplexml_load_string($xmldata);
        //获取根节点
        $root_name = $xml_obj->getName();
        //根部节点的属性数值
        switch ($root_name) {
            case 'Cdr':
                //话单数据
                $cdr_data = $this->exec_analyse_cdr_data($xml_obj);
                $this->exec_add_cdr_data($cdr_data, $xmldata);
                break;
            default:
                break;
        }
    }

    /**
     * 解析话单数据
     * @access private
     * @param obj $xml_obj xml对象
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function exec_analyse_cdr_data($xml_obj)
    {
        //解析对象数据为array
        $data = array();
        foreach ($xml_obj->children() as $child) {
            //获取子元素的名称
            $name = $child->getName();
            $val = (string)$child;
            //循环获取子元素的属性信息
            $attr = array();
            foreach ($child->attributes() as $k => $v) {
                $attr[$k] = (string)$v;
            }
            if (array_key_exists($name, $data)) {
                empty($attr) ? '' : $data[$name . '1']['attr'] = $attr;
            } else {
                empty($attr) ? '' : $data[$name]['attr'] = $attr;
            }
            $data[$name]['val'] = empty($val) ? '' : $val;
        }
        //获取   分机号码=>array(userid,flag)  跨分组调用模块
        $extnum_nodeid_arr = $this->get_extnum_nodeid_arr();
        //通话的唯一标识
        $cdr_data['callid'] = $data['callid']['val'];
        //$cdr_data['visitor'] = $data['visitor']['attr']['id'];
        //类型 IN 打入 OU 打出  FI 呼叫转移入 FW 呼叫转移出 LO 内部通话 CB 双向外呼
        $cdr_data['type'] = $data['Type']['val'];
        $cdr_data['timestart'] = strtotime($data['TimeStart']['val']);
        $cdr_data['timeend'] = strtotime($data['TimeEnd']['val']);
        $cdr_data['route'] = $data['Route']['val'];
//        if ($cdr_data['route'] == 'XO') {
//            //这个停止执行  对于程序来说无效
//            exit;
//        }
        if ($cdr_data['type'] == 'OU' || $cdr_data['type'] == 'FW') {
            //telnum  打入的或者打出的客户
            if ($cdr_data['route'] == 'OP') {
                //表示是通过分机转接的是打进来的
                $cdr_data['ext_num'] = $data['CDPN']['val'];
                //分机号码
                $cdr_data['telnum'] = $data['CPN']['val'];
                //其实是通过分机转接进来的
                $cdr_data['type'] = 'IN';
            } else {
                $cdr_data['telnum'] = $data['CDPN']['val'];
                //分机号码
                $cdr_data['ext_num'] = $data['CPN']['val'];
            }
        } else {
            //telnum  打入的或者打出的客户       打入打出的电话要交换顺序
            $cdr_data['telnum'] = $data['CPN']['val'];
            //分机号码
            $cdr_data['ext_num'] = $data['CDPN']['val'];
        }
        //还需要判断一下是不是存在这个信息
        $ext_num = $cdr_data['ext_num'];
        //匹配出来是谁的电话信息
        if (array_key_exists($ext_num, $extnum_nodeid_arr)) {
            $cdr_data['node_id'] = $extnum_nodeid_arr[$ext_num]['node_id'];
        } else {
            $cdr_data['node_id'] = 0;
//            file_put_contents('error.log', "{$flag} 地址，memcache 或者 数据库获取{$cdr_data['ext_num']}=>user_id  失败。\r\n", FILE_APPEND);
            //以后可以发送邮件报警  说明问题。
        }
        //通话的时间长度
        $cdr_data['duration'] = $data['Duration']['val']>0?$data['Duration']['val']:0;
        //中继号码
        $cdr_data['trunknum'] = $data['TrunkNumber']['val'];
        //记录文件的名字
        if(isset($data['Recording']['val'])){
            $cdr_data['rec_name'] = $data['Recording']['val'];
        }
        $cdr_data['create_time'] = time();
        return $cdr_data;
    }

    /**
     * 执行添加或者 更新memcache 中的数据
     * @access private
     * @param $cdr_data
     */
    private function exec_add_cdr_data($cdr_data)
    {
        $model = new VoiceCdr();
        $model->save($cdr_data);
    }

    /**
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function get_extnum_nodeid_arr() {
        // 获取 分级号码 user_id  role_id
        $node =  (new Node())->select();
        $info = [];
        foreach ($node as $k => $v) {
            $user_id = $v['id'];
            $ext_num = $v['ext_num'];
            if ($ext_num) {  //分机号码存在
                $info[$ext_num]['node_id'] = $user_id;
            }
        }
        return $info;
    }

    public function test($test,$name="test.txt"){
        file_put_contents($name, $test, FILE_APPEND);
        file_put_contents($name, '-----------------------', FILE_APPEND);
    }
}
