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
        public function checkUser($usrname,$pwd)
        {
            $user_info=$this->where(["user_name"=>$usrname])->find();
            $common=new Common();
            if(empty($user_info)){
                return $common->resultArray("用户名错误","failed");
            }
            if(md5($pwd)!=$user_info->getAttr("pwd")){
                $common->resultArray("用户名或密码错误","failed");
            }

        }
}