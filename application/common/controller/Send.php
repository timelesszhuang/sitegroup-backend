<?php

namespace app\common\controller;

use app\common\model\Article;
use app\common\model\Product;
use app\common\model\Question;
use app\common\model\Rejection;
use app\common\model\Site;
use app\common\model\SmsLog;
use app\common\model\User;
use app\common\traits\SendMail;
use app\common\traits\Smsali;
use app\common\model\Node;


class Send extends Common
{
    use Smsali;
    use SendMail;


    public function test_send()
    {
        $this->site_send();
        $this->node_send();
        $this->notaddsend();
    }


    /**
     * 小站点短信发送
     */
    public function site_send()
    {
        $SmsTemplateCode = 'SMS_118715177';
        $where['update_time'] = ['between', [time() - 60 * 5, time()]];
        $where['status'] = 20;

        $where['node_id'] = 68;

        $rejection = new Rejection();
        $sitearr = $rejection->where($where)->select();
        $sarr = [];
        $send = [];
        foreach ($sitearr as $k => $v) {
            $sarr[$v['site_id']][$v['id']] = $v['id'];
        };
        foreach ($sarr as $k => $v) {
            $name = (new Site())->where(['id' => $k])->field('site_name,telephone,mobile,node_id')->find();
            $node_id = $name['node_id'];
            $sitename = $name['site_name'];
            $phone = $name['mobile'];
            $sitecount = count($v);
            $send[$phone]['nodename'][] = $sitename;
            $send[$phone]['mobile'] = $phone;
            $send[$phone]['nodecount'][] = $sitecount;
            $send[$phone]['nodeid'][] = $node_id;
            $send[$phone]['rejection_id'][] = key($v);
        }

        foreach ($send as $k => $v) {
            $sendname = "乐销易平台的";
            $sendcount = array_sum($v['nodecount']);
            $siteerr = $this->send($sendname, $sendcount, $v['mobile'], $SmsTemplateCode);
            echo json_encode($siteerr);
            if (!isset($siteerr->result)) {
                $code = $siteerr->code;
            } else {
                $code = 0;
                $rejection->where($where)->setField('status', 10);
            }
            $newdata[] = [
                'tel_num' => $k,
                'content' => "【乐销易】您的" . $sendname . "有" . $sendcount . "条新的线索,请及时联系，如有疑问请联系：4006-360-163",
                "send_status" => $code,
                'node_id' => "," . implode(',', $v['nodeid']) . ",",
                'send_time' => time(),
                'sg_rejection_id' => "," . implode(',', $v['rejection_id']) . ",",
                'tag_id' => 10,
                'tag_name' => '甩单接收提醒',
            ];
        }

        if (isset($newdata)) {
            $newstatus = (new SmsLog())->insertAll($newdata);
            if ($newstatus) {
                echo("发送成功1");
            }
        }
    }

    /**
     * 节点短信发送
     */
    public function node_send()
    {
        $SmsTemplateCode = 'SMS_118715177';
        $where['update_time'] = ['between', [time() - 60 * 5, time()]];
        $where['nodestatus'] = 20;

        $where['node_id'] = 68;

        $rejection = new Rejection();
        $sitearr = $rejection->where($where)->select();
        $node = [];
        $send = [];
        foreach ($sitearr as $k => $v) {
            $node[$v['node_id']][$v['id']] = $v['id'];
        };
        foreach ($node as $k => $v) {
            $name = (new Node())->where(['id' => $k])->field('name')->find();
            $mobile = (new User())->where(['node_id' => $k])->field('mobile,email')->find();
            $nodename = $name['name'];
            $nodecount = count($v);
            $send[$mobile['mobile']]['nodename'][] = $nodename;
            $send[$mobile['mobile']]['mobile'] = $mobile['mobile'];
            $send[$mobile['mobile']]['nodecount'][] = $nodecount;
            $send[$mobile['mobile']]['nodeid'][] = $k;
            $send[$mobile['mobile']]['rejection_id'][] = key($v);
            $email = $this->getEmailAccount();
            if ($email) {
                $content = "【乐销易】您的" . $nodename . "有" . $nodecount . "条新的线索,请及时联系，如有疑问请联系：4006-360-163";
                $this->phpmailerSend($email['email'], $email['password'], $email["host"], $nodename . "您有新的线索", $mobile['email'], $content, $email["email"]);
            }
        }

        foreach ($send as $k => $v) {
            $sendname = "乐销易平台的";
            $sendcount = array_sum($v['nodecount']);
            $nodeerr = $this->send($sendname, $sendcount, $v['mobile'], $SmsTemplateCode);
            if (!isset($nodeerr->result)) {
                $code = $nodeerr->code;
            } else {
                $code = 0;
                $rejection->where($where)->setField('nodestatus', 10);
            }
            $newdata[] = [
                'tel_num' => $k,
                'content' => "【乐销易】您的" . $sendname . "有" . $sendcount . "条新的线索,请及时联系，如有疑问请联系：4006-360-163",
                "send_status" => $code,
                'send_time' => time(),
                'sg_rejection_id' => "," . implode(',', $v['rejection_id']) . ",",
                'tag_id' => 10,
                'node_id' => "," . implode(',', $v['nodeid']) . ",",
                'tag_name' => '甩单接收提醒',
            ];
        }

        if (isset($newdata)) {
            $newstatus = (new SmsLog())->insertAll($newdata);
            if ($newstatus) {
                echo("发送成功2");
            }
        }
    }


    /**
     * 7天未添加内容发送短信
     */
    public function notaddsend()
    {
        $SmsTemplateCode = 'SMS_122000046';
        $node_id = (new Node())->where(['id' => 68])->field('id')->select();
        foreach ($node_id as $k => $v) {
            $article[$v['id']] = (new Article())->where(['node_id' => $v['id']])->field('create_time')->order('create_time desc')->find();
            $question[$v['id']] = (new Question())->where(['node_id' => $v['id']])->field('create_time')->order('create_time desc')->find();
            $product[$v['id']] = (new Product())->where(['node_id' => $v['id']])->field('create_time')->order('create_time desc')->find();
            $lasttime[$v['id']] = (new SmsLog())->where(['node_id' => ["like",",".$v['id'].','], 'send_status' => 0])->field('send_time')->order('send_time desc')->find();
        }
        $send = [];
        foreach ($node_id as $k => $v) {
            $articletime = strtotime($article[$v['id']]['create_time']);
            $questiontime = strtotime($question[$v['id']]['create_time']);
            $producttime = strtotime($product[$v['id']]['create_time']);
            $lastsendtime = strtotime($lasttime[$v['id']]['send_time']);
            echo $lastsendtime;
            $seventime = time() - 86400 * 7;
            if (($articletime < $seventime) && ($questiontime < $seventime) && ($producttime < $seventime) && ($lastsendtime < $seventime)) {
                $name = (new Node())->where(['id' => $v['id']])->field('name')->find();
                $mobile = (new User())->where(['node_id' => $v['id']])->field('mobile')->find();
                $nodename = $name['name'];
                $nodecount = 7;
                $send[$mobile['mobile']]['nodename'][] = $nodename;
                $send[$mobile['mobile']]['mobile'] = $mobile['mobile'];
                $send[$mobile['mobile']]['nodecount'][] = $nodecount;
                $send[$mobile['mobile']]['nodeid'][] = $v['id'];
            }
        }

        foreach ($send as $k => $v) {
            $sendname = "乐销易平台的";
            $sendcount = 7;
            $nodeerr = $this->send($sendname, $sendcount, $v['mobile'], $SmsTemplateCode);
            if (!isset($nodeerr->result)) {
                $code = $nodeerr->code;
            } else {
                $code = 0;
            }
            $newdata[] = [
                'tel_num' => $v['mobile'],
                'content' => "您的" . $sendname . "网站，超过" . $sendcount . "天未添加内容，请及时添加，如有疑问请联系：4006-360-163",
                "send_status" => $code,
                'send_time' => time(),
                'sg_rejection_id' => 0,
                'tag_id' => 20,
                'node_id' => "," . implode(',', $v['nodeid']) . ",",
                'tag_name' => '内容添加提醒',
            ];
        }


        if (isset($newdata)) {
            $newstatus = (new SmsLog())->insertAll($newdata);
            if ($newstatus) {
                echo("发送成功3");
            }
        }

    }


}




