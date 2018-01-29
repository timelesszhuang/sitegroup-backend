<?php
/**
 * Created by PhpStorm.
 * User: qiangbi
 * Date: 17-6-12
 * Time: 上午9:44
 */

namespace app\common\traits;
use think\Config;
use TopClient;
use AlibabaAliqinFcSmsNumSendRequest;

include(EXTEND_PATH . "taobao-sdk-PHP/TopSdk.php");
trait Smsali
{

    /**
     * 发送甩单短信
     * @param $name 名字
     * @param $count 甩单数量
     * @param $phone 手机
     * @return mixed|\ResultSet|\SimpleXMLElement
     */
    //TODO oldfunction
    public function send($name, $count, $phone,$SmsTemplateCode)
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
        $req->setRecNum($phone);
        $req->setSmsTemplateCode($SmsTemplateCode);
        $resp = $c->execute($req);
        return $resp;
    }



}