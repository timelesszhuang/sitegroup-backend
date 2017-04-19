<?php
// +----------------------------------------------------------------------
// | Description: 登陆类 记住密码等相关操作
// +----------------------------------------------------------------------
// | Author: timelesszhuang <834916321@qq.com>
// +----------------------------------------------------------------------

namespace app\common\controller;

use app\common\controller\Common;
use app\common\model\User;
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
            ["verifyCode", "require", "请填写验证码"]
        ];
        $validate = new Validate($rule);
        //检查参数传递
        if (!$validate->check($post)) {
            $this->resultArray($validate->getError(), "failed");
        }
        //检查验证码
        if (!captcha_check($post["verifyCode"])) {
            $this->resultArray('验证码错误', "failed");
        };
        return (new User())->checkUser($post["user_name"],$post["pwd"]);
    }

    /**
     * 记住密码的
     * @access public
     */
    public function reLogin()
    {
        echo "1111";
    }

}
 