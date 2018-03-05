<?php

namespace app\common\controller;

use app\common\model\BrowseRecord;
use think\Config;
use think\Db;
use think\Request;


class Count extends Common
{

    public $all_count = [];
    public $count = [];

    /**
     * 搜索引擎站点统计
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
//TODO oldfunction
    public function index()
    {
        $param = $this->request->get();
        $user = $this->getSessionUserInfo();

        $starttime = 0;
        $stoptime = time();
        $where = [
        'node_id' => $user["node_id"],
        ];
        //判断前台是否传递参数
        if (isset($param["time"])) {
            list($start_time, $stop_time) = $param['time'];
            $starttime = (!empty(intval($start_time))) ? strtotime($start_time) : $starttime;
            $stoptime = (!empty(intval($stop_time))) ? strtotime($stop_time) : $stoptime;
        }
        $where["create_time"] = ['between', [$starttime, $stoptime]];
        //判断前台有没有传递site——id参数
        if (!empty($param["site_id"])) {
            $where['site_id'] = $param['site_id'];
        }
        $browse = new BrowseRecord();
        $arr = $browse->field('engine,count(id) as keyCount')->where($where)->group('engine')->order("keyCount", "desc")->select();
        $arrcount = $browse->where($where)->count();
        $temp = [];
        foreach ($arr as $k => $v) {
            //组织成前台所需要的百分比数据
            $temp[] = ["value" => round($v['keyCount'] / $arrcount * 100, 2), "name" => $v['engine']];
        }
        return $this->resultArray('', '', $temp);
    }


    /**
     * @return array
     * 统计爬虫
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
//TODO oldfunction
    public function enginecount()
    {
        $param = $this->request->get();
        $user = $this->getSessionUserInfo();
        $where = [
            'node_id' => $user["node_id"],
        ];
        //判断前台是否传递参数
        if (isset($param["time"])) {
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
        if (!empty($param["site_id"])) {
            $where['site_id'] = $param['site_id'];
        }
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
        $engine_alias = Config::get('engine.spider_aliasname');
        foreach ($this->all_count as $k => $v) {
            if (array_key_exists($v['name'], $engine_alias)) {
                $v['name'] = $engine_alias[$v['name']];
                $this->all_count[$k] = $v;
            }
        }
        //重组数组返给前台
        $temp = ["time" => $date_diff, "type" => $this->all_count];
        if (empty($userAgent)) {
            return $this->resultArray( 'failed','没有查询到数据', $temp);
        } else {
            return $this->resultArray( 'success','查询成功', $temp);
        }
    }


    /**
     * @return array
     * 统计爬虫首页
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function en()
    {
        $param = $this->request->get();
        $user = $this->getSessionUserInfo();
        $where = [
            'node_id' => $user["node_id"],
        ];
        //判断前台是否传递参数
        if (isset($param["time"])) {
            list($start_time, $stop_time) = $param['time'];
            $starttime = (!empty(intval($start_time))) ? strtotime($start_time) : time() - 86400 * 14;
            $stoptime = (!empty(intval($stop_time))) ? strtotime($stop_time) : time();
        } //没传参数默认10天
        else {
            $starttime = time() - 86400 * 14;
            $stoptime = time();
        }
        $where["create_time"] = ['between', [$starttime, $stoptime]];
        //判断前台有没有传递site——id参数
        if (!empty($param["site_id"])) {
            $where['site_id'] = $param['site_id'];
        }
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
        //重组数组返给前台
        $engine_alias = Config::get('engine.spider_aliasname');
        foreach ($this->all_count as $k => $v) {
            if (array_key_exists($v['name'], $engine_alias)) {
                $v['name'] = $engine_alias[$v['name']];
                $this->all_count[$k] = $v;
            }
        }
        $temp = ["time" => $date_diff, "type" => $this->all_count];
        return $this->resultArray($temp);
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
//TODO oldfunction
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
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
//TODO oldfunction
    public function pvStatistic()
    {
        $param = $this->request->get();
        $user = $this->getSessionUserInfo();
        $where = [
            'node_id' => $user["node_id"],
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
            $where['site_id'] = $param['site_id'];
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
        if (empty($userpv)) {
            return $this->resultArray('failed','没有查询到数据',  $temp);
        } else {
            return $this->resultArray( 'success','查询成功', $temp);
        }
    }


    /**
     * @return array
     * 统计pv首页
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function show()
    {
        $param = $this->request->get();
        $user = $this->getSessionUserInfo();
        $where = [
            'node_id' => $user["node_id"],
        ];
        //判断前台是否传递参数
        if (isset($param["time"])) {
            list($start_time, $stop_time) = $param['time'];
            $starttime = (!empty(intval($start_time))) ? strtotime($start_time) : time() - 86400 * 14;
            $stoptime = (!empty(intval($stop_time))) ? strtotime($stop_time) : time();
        } else {
            $starttime = time() - 86400 * 14;
            $stoptime = time();

        }
        $where["create_time"] = ['between', [$starttime, $stoptime]];
        //判断前台有没有传递site——id参数
        if (!empty($param["site_id"])) {
            $where['site_id'] = $param['site_id'];
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
        return $this->resultArray($temp);
    }

    /**
     * @param $value
     * 格式化数据
     */
//TODO oldfunction
    public function for1($value)
    {
        $this->count[] = [
            "data" => array_values($value),
            "type" => "line",
        ];
    }

    /**
     * 统计文章
     * @return array
     */
//TODO oldfunction
    public function ArticleCount()
    {
        $count = [];
        $name = [];
        foreach ($this->countArticle() as $item) {
            $count[] = $item["count"];
            $name[] = $item["name"];
        }
        $arr = ["count" => $count, "name" => $name];
        return $this->resultArray('', '', $arr);
    }

//TODO oldfunction
    public function countArticle()
    {
        $user = $this->getSessionUserInfo();
        $where = [
            'node_id' => $user["node_id"],
        ];
        $articleTypes = \app\common\model\Articletype::all($where);
        foreach ($articleTypes as $item) {
            yield $this->foreachArticle($item);
        }


    }

//TODO oldfunction
    public function foreachArticle($articleType)
    {
        $count = \app\common\model\ScatteredTitle::where(["articletype_id" => $articleType->id])->count();
        return ["count" => $count, "name" => $articleType->name];

    }
}
