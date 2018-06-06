<?php

namespace app\common\controller;

use think\Controller;
use think\Request;
use app\common\traits\Obtrait;

class Test extends Controller
{
    use Obtrait;

    public function __construct()
    {
        $token = $this->curl_getwx('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wxba3dd8d2bdf50774&secret=80fe0e106a0d806b023970a08ac7b63c');
        $this->token = json_decode($token);
    }

    public function test()
    {
//        http://salesman.cc/index.php/Companywechat/Writedailyreport/index.html?flag=write
       // $redirect_url = urlencode('http://salesman.cc');
        //dump($redirect_url);
        //die;

        //$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxba3dd8d2bdf50774&redirect_uri=http%3A%2F%2Fsalesman.cc&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect";
        dump($this->token);
    }

}
