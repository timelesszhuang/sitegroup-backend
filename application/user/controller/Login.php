<?php

namespace app\user\controller;

use think\Controller;
use app\admin\model\Site;
use app\user\model\SiteUser;
use think\Config;
use think\Request;
use app\common\controller\Common;
use think\Validate;

class Login extends Controller
{
    /**
     * 本地测试开启下 允许跨域ajax 获取数据
     */
    public function __construct()
    {
        parent::__construct();
        // Allow from any origin
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
            // you want to allow, and if so:
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day
        }

        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                // may also be using PUT, PATCH, HEAD etc
                header("Access-Control-Allow-Methods: GET, POST, PUT,DELETE,OPTIONS");
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
            exit(0);
        }
    }

    /**
     * 执行第一次的登陆操作
     * @access public
     * @author guozhen
     */
    public function login()
    {
        $post = $this->request->post();
        $rule = [
            ["name", "require", "请填写用户名"],
            ["pwd", "require", "请填写密码"],
//            ["verifyCode", "require", "请填写验证码"]
        ];
        $validate = new Validate($rule);
        //检查参数传递
        if (!$validate->check($post)) {
            return $this->resultArray($validate->getError(), "failed");
        }
        //检查验证码
//        if (!captcha_check($post["verifyCode"])) {
//            return  $this->resultArray('验证码错误', "failed");
//        };
        $user_arr=(new SiteUser())->checkUser($post["name"],$post["pwd"]);
        return $this->resultArray($user_arr[0],$user_arr[1],$user_arr[2]);
    }

    /**
     * 返回对象  默认不填为success 否则是failed
     * @param $array 响应数据
     * @return array
     * @return array
     * @author guozhen
     */
    public function resultArray($msg = 0, $stat = '', $data = 0)
    {
        if (empty($stat)) {
            $status = "success";
        } else {
            $status = "failed";
        }
        return [
            'status' => $status,
            'data' => $data,
            'msg' => $msg
        ];
    }

    /**
     * 七天免登录验证
     * @return string
     * @author guozhen
     */
    public function autoLogin()
    {
        $post=$this->request->post();
        if (empty($post["site_id"]) || empty($post["remember"])) {
            return $this->resultArray('', "failed");
        }
        $userInfo = SiteUser::get($post["site_id"]);
        $private = Config::get("crypt.cookiePrivate");
        if($post["remember"]!=md5($userInfo["id"].$userInfo["salt"].$private)){
            return $this->resultArray('', "failed");
        }
        $user_arr=$userInfo->getData();
        unset($user_arr["pwd"]);
        //获取私钥
        $private = Config::get("crypt.cookiePrivate");
        $user_arr["remember"] = md5($user_arr["id"] . $user_arr["salt"] . $private);
        (new SiteUser)->setSession($user_arr);
        $site_info=(new SiteUser())->getSiteInfo($userInfo->id);
        return $this->resultArray('','',["user_info"=>$user_arr,'site_info'=>$site_info]);
    }
}
