<?php

function get_real_ip() {
    $ip = false;
    if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
        $ip = $_SERVER["HTTP_CLIENT_IP"];
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
        if ($ip) {
            array_unshift($ips, $ip);
            $ip = FALSE;
        }
        for ($i = 0; $i < count($ips); $i++) {
            if (!eregi("^(10|172\.16|192\.168)\.", $ips[$i])) {
                $ip = $ips[$i];
                break;
            }
        }
    }
    return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
}

//获取客户的 搜索引擎来源  //表示不是第一次进入这个页面 dier
//从搜索引擎第一次进入的时候分析数据参数第二次的时候传递已经分析好的数据
//搜索引擎
$search_engine = '';
//关键词
$s_val = '';
//ip
$ip = '';
//关键词
$key_word = '';
//地域
$pos = '';
if (!array_key_exists("search_engine", $_GET)) {
    if (array_key_exists('B', $_GET)) {
        $search_engine = 'baidu';
        $s_val = $_GET['B'];
        $key_word = $_GET['B'];
    } else if (array_key_exists('b', $_GET)) {
        $search_engine = 'baidu';
        $s_val = $_GET['b'];
        $key_word = $_GET['b'];
    }else if (array_key_exists('h', $_GET)) {
        $search_engine = 'haosou';
        $s_val = $_GET['h'];
        $key_word = $_GET['h'];
    } else if (array_key_exists('sg', $_GET)) {
        $search_engine = 'sougou';
        $s_val = $_GET['sg'];
        $key_word = $_GET['sg'];
    } else if (array_key_exists('Y', $_GET)) {
        $search_engine = 'youdao';
        $s_val = $_GET['y'];
        $key_word = $_GET['y'];
    } else if (array_key_exists('G', $_GET)) {
        $search_engine = 'google';
        $s_val = $_GET['g'];
        $key_word = $_GET['g'];
    }

    //如果get 没有获取到的则表示来自直接输入   这个参数是什么
    //这个是获取数据查询来源
    if (array_key_exists('HTTP_REFERER', $_SERVER)) {
        $query_string = $_SERVER['HTTP_REFERER'];
    }
    if (empty($query_string)) {
        $query_string = 'input';
    }
    //百度里边的地域信息
    if (strpos($s_val, '20') === 0) {
        $pos = 'shengtu';
    } else {
        $pos = 'qiangbi';
    }
    $ip = get_real_ip();
    $query_string = urlencode($query_string);
    $key_word = urlencode($key_word);
    $search_engine = urlencode($search_engine);
    $s_val = urlencode($s_val);
    $ip = urlencode($ip);
    $pos = urlencode($pos);
} else {
    $query_string = urlencode($_GET['query_string']);
    $key_word = urlencode($_GET['key_word']);
    $search_engine = urlencode($_GET['search_engine']);
    $s_val = urlencode($_GET['s_val']);
    $ip = urlencode($_GET['ip']);
    $pos = urlencode($_GET['pos']);
}
//表示是谁推广的
if(array_key_exists('s', $_GET)){
    $s=$_GET['s'];
}else{
    $s= 0;
}
$get_string = "?query_string=$query_string&key_word=$key_word&search_engine=$search_engine&s_val=$s_val&ip=$ip&pos=$pos&s=$s";
?>
