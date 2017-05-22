<?php

namespace app\user\model;


use app\admin\model\Site;
use think\Config;
use think\Model;
use think\Session;

class SiteUser extends Model
{
    /**
     * 用户验证
     * @param $usrname
     * @param $pwd
     * @return array
     * @author guozhen
     */
    public function checkUser($username, $pwd)
    {
        $user_info = $this::where(["name" => $username])->find();
        if (empty($user_info)) {
            return ["用户名错误", "failed"];
        }
        $user_info_arr = $user_info->toArray();
        if (md5($pwd . $username) != $user_info_arr["pwd"]) {
            return ["用户名或密码错误", "failed"];
        }
        unset($user_info_arr["pwd"]);
        //获取私钥
        $private = Config::get("crypt.cookiePrivate");
        $user_info["remember"] = md5($user_info["id"] . $user_info["salt"] . $private);
        $this->setSession($user_info_arr);
        $this->getSiteInfo($user_info->id);
        return ["登录成功", '', $user_info_arr];
    }

    /**
     * 设置用户的session
     * @param $user
     */
    public function setSession($user)
    {
        Session::set("site_name", $user["name"]);
        Session::set("site_id", $user["id"]);
        Session::set("site_node_id", $user["node_id"]);
    }

    public function getSiteInfo($user_id)
    {
        $siteInfo=Site::where(["user_id"=>$user_id]);
        dump($siteInfo);die;
    }

}
