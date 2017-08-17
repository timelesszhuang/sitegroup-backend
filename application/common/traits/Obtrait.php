<?php
/**
 * Created by PhpStorm.
 * User: qiangbi
 * Date: 17-6-12
 * Time: 上午9:44
 */
namespace app\common\traits;

trait Obtrait{
    /**
     * 停止前台request
     * @param $msg
     */
    public function open_start($msg)
    {
        ob_start();
        print_r(json_encode(['status' => "success", 'data' => '', 'msg' => $msg]));
        $size = ob_get_length();
        header("Content-Length: $size");
        header('Connection: close');
        ob_end_flush();
        ob_flush();
        flush();
    }

    /**
     * 发送curl post请求
     * @param $url
     * @param $data post数据 数组格式
     * @return mixed
     */
    public function curl_post($url,$data)
    {
        //初始化
        $curl = curl_init();
        //设置抓取的url
        curl_setopt($curl, CURLOPT_URL, $url);
        //设置头文件的信息作为数据流输出
        curl_setopt($curl, CURLOPT_HEADER, 0);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //设置post方式提交
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        //执行命令
        $data = curl_exec($curl);
        //关闭URL请求
        curl_close($curl);
        //显示获得的数据
        return $data;
    }

    /**
     * curl get请求
     * @param $url
     * @return mixed
     */
    public function curl_get($url)
    {
        //初始化
        $curl = curl_init();
        //设置抓取的url
        curl_setopt($curl, CURLOPT_URL, $url);
        //设置头文件的信息作为数据流输出
        curl_setopt($curl, CURLOPT_HEADER, 0);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);

        //执行命令
        $data = curl_exec($curl);

        //关闭URL请求
        curl_close($curl);
        //显示获得的数据
        return $data;
    }
}