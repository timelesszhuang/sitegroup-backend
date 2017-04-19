<?php
// +----------------------------------------------------------------------
// | Description: 用户
// +----------------------------------------------------------------------
// | Author: linchuangbin <linchuangbin@honraytech.com>
// +----------------------------------------------------------------------

namespace app\common\model;

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
        public function checkUser($username,$pwd)
        {
            $user_info=$this->where(["user_name"=>$username])->find();
            $common=new Common();
            if(empty($user_info)){
                return $common->resultArray("用户名错误","failed");
            }
            if(md5($pwd.$username)!=$user_info->getAttr("pwd")){
                $common->resultArray("用户名或密码错误","failed");
            }
            unset($user_info["pwd"]);
            $common->resultArray("登录成功!!","",$user_info);
        }
}