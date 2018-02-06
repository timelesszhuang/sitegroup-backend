<?php
namespace Think\Dx;
include('TopSdk.php');
use think\Controller;
use TopClient;
use AlibabaAliqinFcSmsNumSendRequest;
class SendSms extends Controller {

    public function send(){
        $c = new TopClient;
        $c->appkey = "23328827";
        $num='1254';
        $c->secretKey = "757f8a6d2f69aee59e0771b1e9c1d540";
        $req = new AlibabaAliqinFcSmsNumSendRequest;
        $req->setSmsFreeSignName('乐');
        $req->setSmsParam(['sn'=>11]);
        $req->setRecNum("17862520398");
        $req->setSmsTemplateCode('SMS_89160007');
        $resp = $c->execute($req);
        return $resp;
    }

}