<?php
// +----------------------------------------------------------------------
// | Description: 登陆类 记住密码等相关操作
// +----------------------------------------------------------------------
// | Author: timelesszhuang <834916321@qq.com>
// +----------------------------------------------------------------------

namespace app\common\controller;

use app\common\controller\Common;
use app\common\model\User;
use think\Config;
use think\Validate;

class Login extends Common
{
    /**
     * 执行第一次的登陆操作
     * @access public
     * @auther guozhen
     */
    public function login()
    {
        $post = $this->request->post();
        $rule = [
            ["user_name", "require", "请填写用户名"],
            ["pwd", "require", "请填写密码"],
//            ["verifyCode", "require", "请填写验证码"]
        ];
        $validate = new Validate($rule);
        //检查参数传递
        if (!$validate->check($post)) {
            $this->resultArray($validate->getError(), "failed");
        }
        //检查验证码
//        if (!captcha_check($post["verifyCode"])) {
//            return  $this->resultArray('验证码错误', "failed");
//        };
        return (new User())->checkUser($post["user_name"],$post["pwd"]);
    }

    /**
     * 七天免登录验证
     * @return string
     */
    public function autoLogin()
    {
        $post=$this->request->post();
        if (empty($post["rebUserId"]) || empty($post["rebember"])) {
            return $this->resultArray('', "failed");
        }
        $userInfo = User::get($post["rebUserId"]);
        $private = Config::get("crypt.cookiePrivate");
        if($post["rebember"]!=md5($userInfo["id"].$userInfo["salt"].$private)){
            return $this->resultArray('', "failed");
        }
        return $this->resultArray();
    }

    /**
     * 重写
     */
    public function checkSession()
    {

    }
}
 