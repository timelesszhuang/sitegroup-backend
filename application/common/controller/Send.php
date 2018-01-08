<?php

namespace app\common\controller;

use app\common\model\SmsLog;
use GuzzleHttp\Promise\RejectedPromise;
use think\Config;
use think\Controller;
use TopClient;
use app\admin\model\Rejection;
use app\admin\model\Site;
use app\sysadmin\model\Node;
use AlibabaAliqinFcSmsNumSendRequest;
use app\common\model\User;

include(EXTEND_PATH . "taobao-sdk-PHP/TopSdk.php");

class Send extends Controller
{
    static $acsClient = null;

    /**
     * 发送甩单短信
     * @param $name
     * @param $count
     * @return mixed|\ResultSet|\SimpleXMLElement
     */
    public function send($name, $count, $phone)
    {
        $c = new TopClient;
        $c->format = "json";
        $c->appkey = Config::get('smsend.accessKeyId');;
        $c->secretKey = Config::get('smsend.accessKeySecret');
        $req = new AlibabaAliqinFcSmsNumSendRequest;
        $req->setExtend("");
        $req->setSmsType("normal");
        $req->setSmsFreeSignName('乐销易');
        $req->setSmsParam('{"name":"' . $name . '","num":"' . $count . '"}');
        $req->setRecNum("17862520398");
        $req->setSmsTemplateCode('SMS_118715177');
        $resp = $c->execute($req);
        return $resp;
    }

    /**
     * 小站点短信发送
     */
    public function site_send()
    {
        $where['update_time'] = ['between', [time() - 60 * 5, time()]];
        $where['status'] = 20;
        $rejection = new Rejection();
        $sitearr = $rejection->where($where)->select();
        $sarr = [];
        foreach ($sitearr as $k => $v) {
            $sarr[$v['site_id']][$v['id']] = $v['id'];
        };
        foreach ($sarr as $k => $v) {
            $name = (new Site())->where(['id' => $k])->field('site_name,telephone,mobile')->find();
            $sitename = $name['site_name'];
            $phone = $name['mobile'];
            $sitecount = count($v);
            $siteerr = $this->send($sitename, $sitecount, $phone);
            if (!isset($siteerr->result)) {
                $code = $siteerr->code;
            } else {
                $code = 0;
                $rejection->where($where)->setField('status', 10);
            }
            $newdata[] = [
                'tel_num' => $name['mobile'],
                'content' => "【乐销易】您的" . $sitename . "有" . $sitecount . "条新的线索,请及时联系，如有疑问请联系：4006-360-163",
                "send_status" => $code,
                'send_time' => time(),
                'sg_rejection_id' => key($v),
            ];

        }
        if (isset($newdata)) {
            $newstatus = (new SmsLog())->insertAll($newdata);
            if ($newstatus) {
                return $this->resultArray("发送成功");
            }
        }
    }

    /**
     * 节点短信发送
     */
    public function node_send()
    {
        $where['update_time'] = ['between', [time() - 60 * 5, time()]];
        $where['status'] = 20;
        $rejection = new Rejection();
        $sitearr = $rejection->where($where)->select();
        $node = [];
        foreach ($sitearr as $k => $v) {
            $node[$v['node_id']][$v['id']] = $v['id'];
        };
        foreach ($node as $k => $v) {
            $name = (new Node())->where(['id' => $k])->field('name,mobile')->find();
            $mobile = (new User())->where(['node_id'=>$k])->field('mobile')->find();
            $phone = $mobile['mobile'];
            $nodename = $name['name'];
            $nodecount = count($v);
            $nodeerr = $this->send($nodename, $nodecount, $phone);
            if (!isset($nodeerr->result)) {
                $code = $nodeerr->code;
            } else {
                $code = 0;
                $rejection->where($where)->setField('status', 10);
            }
            $newdata[] = [
                'tel_num' => $name['mobile'],
                'content' => "【乐销易】您的" . $nodename . "有" . $nodecount . "条新的线索,请及时联系，如有疑问请联系：4006-360-163",
                "send_status" => $code,
                'send_time' => time(),
                'sg_rejection_id' => key($v),
            ];
        }
        if (isset($newdata)) {
            $newstatus = (new SmsLog())->insertAll($newdata);
            if ($newstatus) {
                return $this->resultArray("发送成功");
            }
        }


    }

    public function resultArray($msg = 0, $stat = '', $data = 0)
    {
        if (empty($stat) || $stat == 'success') {
            $status = "success";
        } else {
            $status = "failed";
        }
        return [
            'status' => $status,
            'data' => $data,
            'msg' => $msg
        ];
    }

}




