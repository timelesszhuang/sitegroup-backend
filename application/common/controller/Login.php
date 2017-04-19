<?php
// +----------------------------------------------------------------------
// | Description: 登陆类 记住密码等相关操作
// +----------------------------------------------------------------------
// | Author: timelesszhuang <834916321@qq.com>
// +----------------------------------------------------------------------

namespace app\common\controller;

use app\common\controller\Common;
use think\Validate;
class Login extends Common
{
    /**
     * 执行第一次的登陆操作
     * @access public
     */
    public function login()
    {
        $post=$this->request->post();
        $rule=[
            ["user_name","require","请填写用户名"],
            ["pwd","require","请填写密码"],
            ["verifyCode","require","请填写验证码"]
        ];
        $validate=new Validate($rule);
        //检查参数传递
        if(!$validate->check($rule)){
            $this->resultArray($validate->getError(),"failed");
        }





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

}
 