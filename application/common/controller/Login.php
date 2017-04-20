<?php
// +----------------------------------------------------------------------
// | Description: 登陆类 记住密码等相关操作
// +----------------------------------------------------------------------
// | Author: timelesszhuang <834916321@qq.com>
// +----------------------------------------------------------------------

namespace app\common\controller;

use app\common\model\User;
use think\Config;
use think\Controller;
use think\Validate;

class Login extends Controller
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
//        if (!captcha_check($post["verifyCode"])) {
//            return  $this->resultArray('验证码错误', "failed");
//        };
        $user_arr=(new User())->checkUser($post["user_name"],$post["pwd"]);
        return $this->resultArray($user_arr[0],$user_arr[1],$user_arr[2]);
    }

    /**
     * 七天免登录验证
     * @return string
     */
    public function autoLogin()
    {
        $post=$this->request->post();
        if (empty($post["user_id"]) || empty($post["remember"])) {
            return $this->resultArray('', "failed");
        }
        $userInfo = User::get($post["user_id"]);
        $private = Config::get("crypt.cookiePrivate");
        if($post["remember"]!=md5($userInfo["id"].$userInfo["salt"].$private)){
            return $this->resultArray('', "failed");
        }
        $user_arr=$userInfo->getData();
        unset($user_arr["pwd"]);
        //获取私钥
        $private = Config::get("crypt.cookiePrivate");
        $user_arr["remember"] = md5($user_arr["id"] . $user_arr["salt"] . $private);
        return $this->resultArray();
    }
    /**
     * 返回对象  默认不填为success 否则是failed
     * @param $array 响应数据
     * @return array
     * @return array
     * @auther guozhen
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
     * 调用resultArray方法
     * 返回json auth——name验证
     * 检测 0 无验证
     * @auther jingzheng
     */

    public function getNoauth()
    {
        $systemConfig = cache('noAuth');
        if (empty($systemConfig)) {
            $systemConfig = $this->getDataList(0);
            cache('noAuth');
        }
        return $this->resultArray('', '', $systemConfig);
    }
}
 