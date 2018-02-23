<?php
/**
 * Created by IntelliJ IDEA.
 * User: qiangbi
 * Date: 18-1-5
 * Time: 下午1:50
 */

namespace app\common\controller;

use Payment\Client\Charge;
use Payment\Common\PayException;

class Pay extends Common
{
    //TODO oldfunction
    public function pay(){
        $config = config("pay");// 这里我假设大家都已经配置好了。不会的请去看配置设置文档


        $channel = 'ali_web';
        $payData = [
            'body'=>'测试',
            'subject'=>'测试',
            'order_no'=>'1231313',
            'amount'=>'0.01',
        ];

        try {
            $payUrl = Charge::run($channel, $config['ali'], $payData);
        } catch (PayException $e) {
            // 异常处理
            exit;
        }
        echo "<script language='javascript' type='text/javascript'>";
        echo "window.location.href='$payUrl'";
        echo "</script>";
    }
}