<?php
/**
 * Created by PhpStorm.
 * User: timeless
 * Date: 17-5-27
 * Time: 下午2:26
 */


ob_start();
print_r(json_encode(['status' => "success", 'data' => '', 'msg' => "正在发送模板,请等待.."]));
echo 'dsdsds';
echo ob_get_level();
$size = ob_get_length();
// send headers to tell the browser to close the connection
header("Content-Length: $size");
header('Connection: close');
ob_end_flush();

ob_flush();
flush();

echo 112323;