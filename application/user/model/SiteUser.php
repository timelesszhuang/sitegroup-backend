<?php

namespace app\user\model;


use app\admin\model\Site;
use app\common\model\LoginLog;
use think\Config;
use think\Model;
use think\Request;
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
        $user_info = $this::where(["account" => $username])->find();
        if (empty($user_info)) {
            return ["用户名错误", "failed",''];
        }

        $user_info_arr = $user_info->toArray();
        if (md5($pwd . $username) != $user_info_arr["pwd"]) {
            return ["用户名或密码错误", "failed",''];
        }
        unset($user_info_arr["pwd"]);
        //获取私钥
        $private = Config::get("crypt.cookiePrivate");
        $user_info_arr["remember"] = md5($user_info["id"] . $user_info["salt"] . $private);
        $this->setSession($user_info_arr);
        $site_info=$this->getSiteInfo($user_info->id);
        $request=Request::instance();
        $ip=$request->ip();
        $location_info="未获取到";
        // 获取ip信息
        $ip_info=$this->get_ip_info($ip);
        if(!empty($ip_info)){
            $location_info=$ip_info['data']['country'].$ip_info['data']['region'].$ip_info['data']['city'];
        }
        //记录日志
        LoginLog::create([
            "ip"=>$request->ip(),
            "node_id"=>$user_info["node_id"],
            "name"=>$user_info["account"],
            "type_name"=>"站点后台",
            "location"=>$location_info,
            "site_id"=>$user_info["id"]
        ]);
        return ["登录成功", '', ["user_info"=>$user_info_arr,"site_info"=>$site_info]];
    }

    /**
     * 设置用户的session
     * @param $user
     */
    public function setSession($user)
    {
        Session::set("login_site", $user);
    }

    /**
     * 获取登录网站信息
     * @param $user_id
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getSiteInfo($user_id)
    {
        $siteInfo=Site::where(["user_id"=>$user_id])->field("id,domain,site_name")->select();
        return $siteInfo;
    }

}
