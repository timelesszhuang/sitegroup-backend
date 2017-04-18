<?php
// +----------------------------------------------------------------------
// | Description: 登陆类 记住密码等相关操作
// +----------------------------------------------------------------------
// | Author: timelesszhuang <834916321@qq.com>
// +----------------------------------------------------------------------

namespace app\index\controller;


class Login extends Common
{
    /**
     * 执行第一次的登陆操作
     * @access public
     */
    public function login()
    {
        $userModel = model('User');
        $param = $this->param;
        $username = $param['username'];
        $password = $param['password'];
        $verifyCode = !empty($param['verifyCode']) ? $param['verifyCode'] : '';
        $isRemember = !empty($param['isRemember']) ? $param['isRemember'] : '';
        $data = $userModel->login($username, $password, $verifyCode, $isRemember);
        if (!$data) {
            return $this->resultArray(['error' => $userModel->getError()]);
        }
        return $this->resultArray(['data' => $data]);

    }

    /**
     * 记住密码的
     * @access public
     */
    public function reLogin()
    {
            echo "1111";
    }

    /**
     * 获取验证码url 返回给前台
     * @return string
     */
    public function getCaptcha()
    {
        return json_encode(["captchaUrl" => captcha_src()]);
    }

}
 