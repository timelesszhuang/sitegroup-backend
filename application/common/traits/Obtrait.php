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

    /**
     * 图片文件转base64
     * @param $image_file
     * @return string
     */
    public function base64EncodeImage ($image_file) {
        $base64_image = '';
        $image_info = getimagesize($image_file);
        $image_data = fread(fopen($image_file, 'r'), filesize($image_file));
        $base64_image = 'data:' . $image_info['mime'] . ';base64,' . chunk_split(base64_encode($image_data));
        return $base64_image;
    }

    /**
     * @param $ip
     * @return mixed
     * 调用接口根据ip查询地址
     */
    public function get_ip_info($ip)
    {
        $curl = curl_init(); //这是curl的handle
        $url = "http://ip.taobao.com/service/getIpInfo.php?ip=$ip";
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($curl, CURLOPT_HEADER, 0); //don't show header
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //相当关键，这句话是让curl_exec($ch)返回的结果可以进行赋值给其他的变量进行，json的数据操作，如果没有这句话，则curl返回的数据不可以进行人为的去操作（如json_decode等格式操作）
        curl_setopt($curl, CURLOPT_TIMEOUT, 2);
        $data = curl_exec($curl);
        return json_decode($data, true);
    }

    /**
     * 解压缩zip文件
     * @param $src 原始zip目录
     * @param $obj 解压缩后的目录
     * @param $directiry 解压的目录
     */
    public function ZipArchive($src,$obj,$directiry)
    {
        $made_path='';
        $zip=new \ZipArchive();
        if($zip->open($src)==true){
            $zip->extractTo($obj);
            for($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);
                $fileinfo = pathinfo($filename);
                if(isset($fileinfo["dirname"])){
                    if($fileinfo["dirname"]!="." && strstr($fileinfo["dirname"],"/")===false){
                        $made_path= $directiry.$fileinfo["dirname"];
                        chmod($obj.$fileinfo["dirname"],0755);
                        break;
                    }
                }
            }
            $zip->close();
        }
        return $made_path;
    }

    /**
     * 删除目录下所有文件
     * @param $dir
     * @return bool
     */
    public function del_dir($dir) {
        if (!is_dir($dir)) {
            return false;
        }
        $handle = opendir($dir);
        while (($file = readdir($handle)) !== false) {
            if ($file != "." && $file != "..") {
                is_dir("$dir/$file") ? $this->del_dir("$dir/$file") : @unlink("$dir/$file");
            }
        }
        if (readdir($handle) == false) {
            closedir($handle);
            @rmdir($dir);
        }
    }


    public function checkZipDirectory($src,$name)
    {
        $zip=new \ZipArchive();
        if($zip->open($src)==true){
            $zip->extractTo($obj);
            for($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);
                $fileinfo = pathinfo($filename);
                if(isset($fileinfo["dirname"])){
                    if($fileinfo["dirname"]!="." && strstr($fileinfo["dirname"],"/")===false){
                        $made_path= $directiry.$fileinfo["dirname"];
                        chmod($obj.$fileinfo["dirname"],0755);
                        break;
                    }
                }
            }
            $zip->close();
        }

    }
}