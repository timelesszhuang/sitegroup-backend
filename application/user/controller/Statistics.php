<?php

namespace app\user\controller;

use app\common\model\BrowseRecord;
use think\Db;
use think\Request;
use app\common\controller\Common;
use think\Session;
use think\Validate;

class Statistics extends Common
{

    public $all_count = [];
    public $count = [];

    /**
     * 搜索引擎站点统计
     * @return \think\Response
     */
    public function index()
    {
        $limits = $this->getLimit();
        $site_id = Session::get("website")["id"];
        $node_id = Session::get('login_site')["node_id"];
        $where = [];
        if (!empty($site_id)) {
            $where['site_id'] = $site_id;
        }
        $where["node_id"] = $node_id;
        return $this->resultArray('', '', (new \app\admin\model\Pv())->getAll($limits['limit'], $limits['rows'], $where));
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
        //
    }

    /**
     * 显示指定的资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
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
     *
     * @param  int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }

    /**
     * 小网站用户存储站点信息
     * @return array
     */
    public function siteInfo()
    {

    }

    /**
     * 设置session 全部都放进去 以后有用
     * @param $site_id
     * @param $site_name
     */
    public function setSession($site_info)
    {

    }

    /**
     * @return array
     * 统计爬虫
     */
    public function enginecount()
    {
        $param = $this->request->get();
        $site_id = Session::get("website")["id"];
        $user = $this->getSessionUser();
        $node_id = $user["user_node_id"];
        $where = [
            'node_id' => $node_id,
            'site_id' => $site_id,
        ];
        //判断前台是否传递参数
        if (!empty($param["time"])) {
            list($start_time, $stop_time) = $param['time'];
            $starttime = (!empty(intval($start_time))) ? strtotime($start_time) : time() - 86400 * 9;
            $stoptime = (!empty(intval($stop_time))) ? strtotime($stop_time) : time();
        } //没传参数默认10天
        else {
            $starttime = time() - 86400 * 9;
            $stoptime = time();
        }
        $where["create_time"] = ['between', [$starttime, $stoptime]];
        //判断前台有没有传递site——id参数
        $userAgent = Db::name("useragent")->where($where)->field("engine,create_time")->select();
        $Agent = [];
        $Engine = [];
        //循环userAgent 组织成vue前台series所需要的数据
        //二维数组 引擎名字为键值 里面一层时间为键值 下面时间所拥有的值
        foreach ($userAgent as $v) {
            $engine = $v['engine'];
            //in_array判断$engine是否在$Engine,数组递加
            if (!in_array($engine, $Engine)) {
                array_push($Engine, $engine);
            }
            //格式化时间
            $date = date('m-d', $v['create_time']);
            //array_key_exists 判断数组里是否有这个数据 没有的话置为空
            if (!array_key_exists($engine, $Agent)) {
                $Agent[$engine] = [];
            }

            if (array_key_exists($date, $Agent[$engine])) {
                $Agent[$engine][$date] += 1;
            } else {
                $Agent[$engine][$date] = 1;
            }
        }
        //格式化时间
        $date_diff = $this->get_date_diff($starttime, $stoptime);
        //当前时间下的数据为空置为0
        foreach ($Engine as $engine) {
            foreach ($date_diff as $date) {
                //对时间排序
                ksort($Agent[$engine]);
                //判断数组里是否有这个数据 没有的话把当前时间下的值置为0
                if (!array_key_exists($date, $Agent[$engine])) {
                    $Agent[$engine][$date] = 0;
                }
            }
        }
        //格式化数据 array_walk() 数组的键名和键值是参数。
        array_walk($Agent, [$this, "formatter"]);
        if (empty($userAgent)) {
            $this->all_count[0] = [
                "name" => "sougou",
                "data" => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                "type" => "line",
                "stack" => '总量',
            ];
            $this->all_count[1] = [
                "name" => "baidu",
                "data" => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                "type" => "line",
                "stack" => '总量',
            ];
        }
        //重组数组返给前台
        $temp = ["time" => $date_diff, "type" => $this->all_count];
        return $this->resultArray('', '', $temp);
    }

    /**
     *获取某段时间内的时间显示
     * @param $stime
     * @param $etime
     * @return array
     */
    public function get_date_diff($stime, $etime)
    {
        //将起始时间转换成 2014-05 年月的格式
        $st = strtotime(date("Y-m-d", $stime));
        $et = strtotime(date("Y-m-d", $etime));
        $no_time = false;
        if ($et > time()) {
            $et = time();
            $no_time = true;
        }
        $arr = array();
        while ($st) {
            array_push($arr, date("m-d", $st));
            $st = strtotime("+1 day", $st);
            if ($st >= $et) {
                break;
            }
        }
        if (!$no_time) {
            array_push($arr, date("m-d", $st));
        }
        //返回格式 array("2014-01","2014-02",.....)的时间戳形式
        return $arr;
    }

    /**
     * @param $value
     * @param $key
     * 格式化数据
     */
    public function formatter($value, $key)
    {
        $this->all_count[] = [
            "name" => $key,
            "data" => array_values($value),
            "type" => "line",
            "stack" => '总量',
        ];
    }

    /**
     * @return array
     * 统计pv
     */
    public function pv()
    {
        $param = $this->request->get();
        $site_id = Session::get("website")["id"];
        $user = $this->getSessionUser();
        $node_id = $user["user_node_id"];
        $where = [
            'node_id' => $node_id,
            'site_id' => $site_id,
        ];
        //判断前台是否传递参数
        if (isset($param["time"])) {
            list($start_time, $stop_time) = $param['time'];
            $starttime = (!empty(intval($start_time))) ? strtotime($start_time) : time() - 86400 * 9;
            $stoptime = (!empty(intval($stop_time))) ? strtotime($stop_time) : time();
        } else {
            $starttime = time() - 86400 * 9;
            $stoptime = time();
        }
        $where["create_time"] = ['between', [$starttime, $stoptime]];
        //判断前台有没有传递site——id参数
        if (!empty($param["site_id"])) {
            $where['site_id'] = $site_id;
        }
        $userpv = Db::name("pv")->where($where)->field("node_id,create_time")->select();
        //循环$userpv 组织成vue前台series所需要的数据
        //二维数组 名字为键值 里面一层时间为键值 下面时间所拥有的值
        $Pv = [];
        $pv = [];
        foreach ($userpv as $v) {
            $pvid = $v['node_id'];
            //in_array判断$pv是否在$Pv,数组递加
            if (!in_array($pvid, $pv)) {
                array_push($pv, $pvid);
            }
            //格式化时间
            $date = date('m-d', $v['create_time']);
            //array_key_exists 判断数组里是否有这个数据 没有的话置为空
            if (!array_key_exists($pvid, $Pv)) {
                $Pv[$pvid] = [];
            }
            if (array_key_exists($date, $Pv[$pvid])) {
                $Pv[$pvid][$date] += 1;
            } else {
                $Pv[$pvid][$date] = 1;
            }
        }
        //格式化时间
        $date_diff = $this->get_date_diff($starttime, $stoptime);
        //当前时间下的数据为空置为0
        foreach ($pv as $pvid) {
            foreach ($date_diff as $date) {
                //对时间排序
                ksort($Pv[$pvid]);
                //当前时间下的数据为空置为0
                if (!array_key_exists($date, $Pv[$pvid])) {
                    $Pv[$pvid][$date] = 0;
                }
            }
        }
        //array_walk() 数组的键名和键值是参数。
        array_walk($Pv, [$this, "for1"]);
        $temp = ["time" => $date_diff, "type" => $this->count];
        return $this->resultArray('', '', $temp);
    }

    /**
     * @param $value
     * 格式化数据
     */
    public function for1($value)
    {
        $this->count[] = [
            "data" => array_values($value),
            "type" => "line",
        ];
    }

}
