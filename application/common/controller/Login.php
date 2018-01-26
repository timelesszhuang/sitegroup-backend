<?php
// +----------------------------------------------------------------------
// | Description: 登陆类 记住密码等相关操作
// +----------------------------------------------------------------------
// | Author: timelesszhuang <834916321@qq.com>
// +----------------------------------------------------------------------

namespace app\common\controller;

use app\common\model\LoginLog;
use app\common\model\SiteUser;
use app\common\model\User;
use app\common\traits\Obtrait;
use app\common\traits\Osstrait;
use app\common\model\Node;
use think\Config;
use think\Request;
use think\Session;
use think\Validate;

class Login extends Common
{
    use Osstrait;
    use Obtrait;

    /**
     * 本地测试开启下 允许跨域ajax 获取数据
     */
    public function __construct()
    {
        parent::__construct();
        // Allow from any origin
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
            // you want to allow, and if so:
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day
        }
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                // may also be using PUT, PATCH, HEAD etc
                header("Access-Control-Allow-Methods: GET, POST, PUT,DELETE,OPTIONS");
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
            exit(0);
        }
    }


    /**
     * 图片上传到 oss相关操作
     * @access public
     */
    //TODO 删除
    public function imageupload()
    {
        $request = Request::instance();
        $whitelist = Config::get('whitelist.whitelist');
        if (!in_array(parse_url($request->header('Origin'))['host'], $whitelist)) {
            return [
                'msg' => '请登录',
                'status' => false
            ];
        }
        $dest_dir = 'pic/';
        $endpoint = Config::get('oss.endpoint');
        $bucket = Config::get('oss.bucket');
        $file = $request->file("img");
        $localpath = ROOT_PATH . "public/upload/";
        $fileInfo = $file->move($localpath);
        $object = $dest_dir . $fileInfo->getSaveName();
        $localfilepath = $localpath . $fileInfo->getSaveName();
        $put_info = $this->ossPutObject($object, $localfilepath);
        unlink($localfilepath);
        $msg = '上传图片失败';
        $url = '';
        $status = false;
        if ($put_info['status']) {
            $msg = '上传图片成功';
            $status = true;
            $url = sprintf("https://%s.%s/%s", $bucket, $endpoint, $object);
        }
        return [
            'msg' => $msg,
            "url" => $url,
            'status' => $status,
        ];
    }


    /**
     * 执行第一次的登陆操作
     * @access public
     * @author jingyang
     */
    public function login()
    {
        $data = Request::instance()->post();
        $rule = [
            ["user_name", "require", "请填写用户名"],
            ["password", "require", "请填写密码"],
            ["verify_code", "require", "请填写验证码"],
            ["login_type", "require", "未知用户类型"]
        ];
        $validate = new Validate($rule);
        try {
            //验证字段
            if (!$validate->check($data)) {
                $error = $validate->getError();
                /** @var string $error */
                exception($error);
            }
//            验证验证码
//            if (!captcha_check($data["verify_code"])) {
//                exception('验证码错误');
//            };
            //返回结果容器
            $return = [];
            //登录日志容器
            //登录信息容器
            $user_info = [];
            if ($data['login_type'] == 'node') {
                $user_info = (new User())->checkUserLogin($data["user_name"], $data["password"]);
            } elseif ($data['login_type'] == 'site') {
                $user_info = (new SiteUser())->checkUserLogin($data["user_name"], $data["password"]);
                $log['site_id'] = $user_info['$user_info'];
            } else {
                exception('未知错误');
            }
            // 获取ip信息
            $request = Request::instance();
            $ip = $request->ip();
            $user_info['ip'] = $ip;
            //如果存在
            $return["remember_key"] =(isset($data['remember'])&&$data['remember'])?$this->getNewRememberStr($user_info['id'], $data['login_type']):'';
            $return["login_type"] = $user_info['type'];
            $return["login_id"] = $user_info['id'];
            //记录日志
            $this->setLoginLog($user_info);
            //设置session信息
            $this->setLoginSession($user_info);
            return $this->resultArray('success', '登陆成功', $return);
        } catch (\Exception $exception) {
            return $this->resultArray("failed", $exception->getMessage());
        }
    }

    public function setLoginSession($user_info)
    {
        Session::set('login_id', $user_info["id"]);
        Session::set('login_type', $user_info["type"]);
        Session::set('login_ip', $user_info["ip"]);
    }

    public function setLoginLog($user_info){
        $request = Request::instance();
        $ip = $request->ip();
        $location_info = "未获取到";
        $ip_info = $this->get_ip_info($ip);
        if (!empty($ip_info)) {
            $location_info = $ip_info['data']['country'] . $ip_info['data']['region'] . $ip_info['data']['city'];
        }
        $log = [];
        $log['ip'] = $ip;
        $log['node_id'] = $user_info['node_id'];
        $log['name'] = $user_info['name'];
        $log['type_name'] = $user_info['type_name'];
        $log['location'] = $location_info;
        LoginLog::create($log);
    }

    /**
     * 七天免登录验证
     * @return array
     * @author guozhen
     */
    public function autoLogin()
    {
        $data = Request::instance()->post();
        $rule = [
            ["remember_key", "require", "未获取到自动登录key"],
            ["login_id", "require", "未获取到自动登录用户id"],
            ["login_type", "require", "未知的用户类型"]
        ];
        $validate = new Validate($rule);
        try {
            //验证字段
            if (!$validate->check($data)) {
                $error = $validate->getError();
                /** @var string $error */
                exception($error);
            }
            //登录信息容器
            if ($data['login_type'] == 'node') {
                $user_info = (new User())->checkUserLogin($data["login_id"], $data["remember_key"],'auto');
            } elseif ($data['login_type'] == 'site') {
                $user_info = (new SiteUser())->checkUserLogin($data["login_id"], $data["remember_key"],'auto');
                $log['site_id'] = $user_info['$user_info'];
            } else {
                exception('未知错误');
            }
            // 获取ip信息
            $request = Request::instance();
            $ip = $request->ip();
            $user_info['ip'] = $ip;
            //如果存在
            $return["remember_key"] =(isset($data['remember_key'])&&$data['remember_key'])?$this->getNewRememberStr($user_info['id'], $data['login_type']):'';
            $return["login_type"] = $user_info['type'];
            $return["login_id"] = $user_info['id'];
            //设置session信息
            $this->setLoginSession($user_info);
            return $this->resultArray('success', '自动登陆成功', $return);
        } catch (\Exception $exception) {
            return $this->resultArray("failed", $exception->getMessage());
        }
    }

    /**
     * 调用resultArray方法
     * 返回json auth——name验证
     * 检测 0 无验证
     * @author jingzheng
     */

    public function getNoauth()
    {
        $systemConfig = cache('noAuth');
        if (empty($systemConfig)) {
            $systemConfig = $this->getDataList(0);
            cache('noAuth', $systemConfig);
        }
        return $this->resultArray('', '', $systemConfig);
    }

    /**
     * 获取配置列表
     * 重组数组
     * @author jingzheng
     * */

    public function getDataList($auth)
    {
        $SystemConfig = new \app\common\model\SystemConfig();
        $auth_data = $SystemConfig->where(["need_auth" => $auth])->select();
        $data = array();
        foreach ($auth_data as $key => $val) {
            $data[$val['name']] = $val['value'];
        }
        return $data;
    }

}
 