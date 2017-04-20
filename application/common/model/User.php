<?php
// +----------------------------------------------------------------------
// | Description: 用户
// +----------------------------------------------------------------------
// | Author: linchuangbin <linchuangbin@honraytech.com>
// +----------------------------------------------------------------------

namespace app\common\model;

use think\Config;
use think\Db;
use think\Model;
use app\common\controller\Common;

class User extends Model
{
    /**
     * 用户验证
     * @param $usrname
     * @param $pwd
     * @return array
     * @auther guozhen
     */
    public function checkUser($username, $pwd)
    {
        $common = new Common();
        $user_info = $this::where(["user_name" => $username])->find();
        if(empty($user_info)){
            return $common->resultArray("用户名错误", "failed");
        }
        $user_info_arr = $user_info->toArray();
        if (md5($pwd . $username) != $user_info_arr["pwd"]) {
            return $common->resultArray("用户名或密码错误", "failed");
        }
        unset($user_info["pwd"]);
        //获取私钥
        $private = Config::get("crypt.cookiePrivate");
        $user_info["remember"] = md5($user_info["id"] . $user_info["salt"] . $private);
        return $common->resultArray("登录成功!!", "", $user_info);
    }
}