<?php


namespace app\common\controller;

use app\sysadmin\model\Node;
use think\Controller;
use think\Request;
class Omapi extends Controller
{
    /**
     * 显示资源列表
     * @return \think\Response
     */
    public function index()
    {
        //
//        $xmldata = file_get_contents('php://input');
                $xmldata = <<<xmldata
<Cdr id="43320160504165726-0">
  <callid>53490</callid>
  <outer id="242" />
  <TimeStart>20160504165621</TimeStart>
  <Type>OU</Type>
  <Route>IP</Route>
  <CPN>770</CPN>
  <CDPN>018339707630</CDPN>
  <TimeEnd>20160504165726</TimeEnd>
  <Duration>30</Duration>
  <TrunkNumber>568117250</TrunkNumber>
  <Recording>20160504/329_018339707630_20160504-165657_53490</Recording>
</Cdr>
xmldata;
        file_put_contents('a.txt', 'DSADAS', FILE_APPEND);
        file_put_contents('a.txt', $xmldata, FILE_APPEND);
        $this->analyse_data($xmldata);
    }


    /**
     * 解析获取到的xml数据
     * @access private
     * @param xml数据 $xmldata xml数据  cdr 话单 /answered信息  answer信息
     * @param string $flag 标识是济南的还是河南
     */
    private function analyse_data($xmldata)
    {
        //xml 转换为对象
        $xml_obj = simplexml_load_string($xmldata);
        //dump($xml_obj);die;
        //获取根节点
        $root_name = $xml_obj->getName();
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
    private function exec_analyse_cdr_data($xml_obj)
    {
        //解析对象数据为array
        $data = $this->resolve($xml_obj);
        //dump($data);die;
        //获取   分机号码=>array(userid)
        $extnum_useridflag_arr =$this->get_extnum_useridflag_arr();
        //通话的唯一标识
        $cdr_data['callid'] = $data['callid']['val'];
        //$cdr_data['visitor'] = $data['visitor']['attr']['id'];
        //类型 IN 打入 OU 打出  FI 呼叫转移入 FW 呼叫转移出 LO 内部通话 CB 双向外呼
        $cdr_data['type'] = $data['Type']['val'];
        $cdr_data['timestart'] = strtotime($data['TimeStart']['val']);
        $cdr_data['timeend'] = strtotime($data['TimeEnd']['val']);
        $cdr_data['route'] = $data['Route']['val'];

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
        if (array_key_exists($ext_num, $extnum_useridflag_arr)) {
            $cdr_data['node_id'] = $extnum_useridflag_arr[$ext_num]['node_id'];
            //10表示弹屏 20 表示不弹屏
        }
        //通话的时间长度
        $cdr_data['duration'] = $data['Duration']['val'];
        //中继号码
        $cdr_data['trunknum'] = $data['TrunkNumber']['val'];
        //记录文件的名字
        $cdr_data['rec_name'] = $data['Recording']['val'];
        $cdr_data['addtime'] = time();
        dump($cdr_data);die;
        return $cdr_data;

    }

    /**
     * @return array
     *
     */
    public function get_extnum_useridflag_arr() {
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

    public function resolve($xml_obj)
    {
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
        return $data;
    }
    private function exec_add_cdr_data($cdr_data)
    {
        //往memcache 中存储数据
        $mem = get_mem_obj();
        //获取应答的模式下的信息
        $user_id = $cdr_data['user_id'];
        $answered_data = $mem->get($user_id);
        //根据answered_data数据获取数据
        $cdr_data['cus_id'] = $answered_data['cus_id'] ?: 0;
        $cdr_data['cus_name'] = $answered_data['cus_name'] ?: '';
        $cdr_data['contact_id'] = $answered_data['contact_id'] ?: 0;
        $cdr_data['contact_name'] = $answered_data['contact_name'] ?: '';
        if ($cdr_data['duration']) {
            //成功的话 这个返回值是 $status
            $status = D('Home/VoiceCdr')->insert_cdr_data($cdr_data);
            if (!$status) {
                file_put_contents('error.log', "cdr数据解析添加到数据库失败");
                return;
            } else {
                $cdr_data['id'] = $status;
            }
        } else {
            M("VoiceCdrNotconnect")->add($cdr_data);
        }
        print_r($cdr_data);
        print_r($answered_data);
        //status==10    20 表示没有弹屏的权限   30 表示没有获取到 user_id
        if ($cdr_data['status'] == '10') {
            $cdr_callid = $cdr_data['callid'];
            $answered_callid = $answered_data['callid'];
            print_r($answered_data);
            if ($answered_callid) {
                //打出的电话数据分析
                if ($cdr_callid == $answered_callid) {
                    $answered_data['cdr_info'] = $cdr_data;
                    $mem->set($user_id, $answered_data, 28800);
                } else {
                    //如果不相等的 删除该键值
                    $mem->delete($user_id);
                }
            } else {
                //这个是打进来的电话   没有callid的情况
                $answered_data['cdr_info'] = $cdr_data;
                $mem->set($user_id, $answered_data, 28800);
            }
        }
    }

}
