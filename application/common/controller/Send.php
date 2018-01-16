<?php

namespace app\common\controller;

use app\admin\model\Article;
use app\admin\model\Product;
use app\admin\model\Question;
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
use app\common\traits\Smsali;


class Send extends Controller
{
    use Smsali;

    /**
     * 小站点短信发送
     */
    public function site_send()
    {
        $SmsTemplateCode='SMS_118715177';
        $where['update_time'] = ['between', [time() - 60 * 5, time()]];
        $where['status'] = 20;
        $rejection = new Rejection();
        $sitearr = $rejection->where($where)->select();
        $sarr = [];
        foreach ($sitearr as $k => $v) {
            $sarr[$v['site_id']][$v['id']] = $v['id'];
        };
        foreach ($sarr as $k => $v) {
            $name = (new Site())->where(['id' => $k])->field('site_name,telephone,mobile,node_id')->find();
            $node_id = $name['node_id'];
            $sitename = $name['site_name'];
            $phone = $name['mobile'];
            $sitecount = count($v);
            $siteerr = $this->send($sitename, $sitecount, $phone,$SmsTemplateCode);
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
                'node_id'=>$node_id,
                'send_time' => time(),
                'sg_rejection_id' => key($v),
                'tag_id'=>10,
                'tag_name'=>'甩单接收提醒',
            ];

        }
        if (isset($newdata)) {
            $newstatus = (new SmsLog())->insertAll($newdata);
            if ($newstatus) {
                exit ("发送成功");
            }
        }
    }

    /**
     * 节点短信发送
     */
    public function node_send()
    {
        $SmsTemplateCode='SMS_118715177';
        $where['update_time'] = ['between', [time() - 60 * 5, time()]];
        $where['nodestatus'] = 20;
        $rejection = new Rejection();
        $sitearr = $rejection->where($where)->select();
        $node = [];
        foreach ($sitearr as $k => $v) {
            $node[$v['node_id']][$v['id']] = $v['id'];
        };
        foreach ($node as $k => $v) {
            $name = (new Node())->where(['id' => $k])->field('name')->find();
            $mobile = (new User())->where(['node_id' => $k])->field('mobile')->find();
            $nodename = $name['name'];
            $nodecount = count($v);
            $nodeerr = $this->send($nodename, $nodecount, $mobile['mobile'],$SmsTemplateCode);
            if (!isset($nodeerr->result)) {
                $code = $nodeerr->code;
            } else {
                $code = 0;
                $rejection->where($where)->setField('nodestatus', 10);
            }
            $newdata[] = [
                'tel_num' => $mobile['mobile'],
                'content' => "【乐销易】您的" . $nodename . "有" . $nodecount . "条新的线索,请及时联系，如有疑问请联系：4006-360-163",
                "send_status" => $code,
                'send_time' => time(),
                'sg_rejection_id' => key($v),
                'tag_id'=>10,
                'node_id'=>$k,
                'tag_name'=>'甩单接收提醒',
            ];
        }
        if (isset($newdata)) {
            $newstatus = (new SmsLog())->insertAll($newdata);
            if ($newstatus) {
                exit("发送成功");
            }
        }
    }


    /**
     * 7天未添加内容发送短信
     */
    public function notaddsend()
    {
        $SmsTemplateCode = 'SMS_122000046';
        $node_id = (new Node())->field('id')->select();
        foreach ($node_id as $k => $v) {
            $article[$v['id']] = (new Article())->where(['node_id' => $v['id']])->field('create_time')->order('create_time desc')->find();
            $question[$v['id']] = (new Question())->where(['node_id' => $v['id']])->field('create_time')->order('create_time desc')->find();
            $product[$v['id']] = (new Product())->where(['node_id' => $v['id']])->field('create_time')->order('create_time desc')->find();
            $lasttime[$v['id']] = (new SmsLog())->where(['node_id' => $v['id'],'send_status'=>0])->field('send_time')->order('send_time desc')->find();

        }
        foreach ($node_id as $k => $v) {
            $articletime =strtotime($article[$v['id']]['create_time']);
            $questiontime = strtotime($question[$v['id']]['create_time']);
            $producttime = strtotime($product[$v['id']]['create_time']);
            $lastsendtime = strtotime($lasttime[$v['id']]['send_time']);
            $seventime = time()-86400*7;
            if(($articletime < $seventime) && ($questiontime < $seventime) && ($producttime < $seventime)&& ($lastsendtime < $seventime)){
                $name = (new Node())->where(['id' => $v['id']])->field('name')->find();
                $mobile = (new User())->where(['node_id' => $v['id']])->field('mobile')->find();
                $nodename = $name['name'];
                $nodecount = 7;
                $nodeerr = $this->send($nodename, $nodecount, $mobile['mobile'],$SmsTemplateCode);
                if (!isset($nodeerr->result)) {
                    $code = $nodeerr->code;
                } else {
                    $code = 0;
                }
                $newdata[] = [
                    'tel_num' => $mobile['mobile'],
                    'content' =>"您的". $nodename ."网站，超过".$nodecount ."天未添加内容，请及时添加，如有疑问请联系：4006-360-163",
                    "send_status" => $code,
                    'send_time' => time(),
                    'sg_rejection_id' => 0,
                    'tag_id'=>20,
                    'node_id'=>$v['id'],
                    'tag_name'=>'内容添加提醒',
                    ];
            }
        }
        if (isset($newdata)) {
            $newstatus = (new SmsLog())->insertAll($newdata);
            if ($newstatus) {
                exit("发送成功");
            }
        }

    }


}




