<?php
// +----------------------------------------------------------------------
// | Description: 登陆类 记住密码等相关操作
// +----------------------------------------------------------------------
// | Author: timelesszhuang <834916321@qq.com>
// +----------------------------------------------------------------------

namespace app\common\controller;

use app\common\model\Site;
use app\common\exception\ProcessException;
use app\common\model\LoginLog;
use app\common\model\SiteUser;
use app\common\model\User;
use app\common\traits\Obtrait;
use app\common\traits\Osstrait;
use think\Config;
use think\Request;
use think\Session;
use think\Validate;

/**
 * @title 用户登录
 * @description 用户登录
 * @group 登录
 */
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
     * @title 登录接口
     * @description 接口说明
     * @author 孙靖洋
     * @url login
     * @method GET
     * @module 用户模块
     * @return array
     * @return_data remember_key:11;
     * @return_data remember_key1:11;
     * @throws \Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
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
                Common::processException($error);
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
                $log['site_id'] = $user_info['id'];
            } else {
                Common::processException('未知错误');
            }
            // 获取ip信息
            $request = Request::instance();
            $ip = $request->ip();
            $user_info['ip'] = $ip;
            $user_info['type_name'] = $data['login_type'];
            //如果存在
            $return["remember_key"] = (isset($data['remember']) && $data['remember']) ? $this->getNewRememberStr($user_info['id'], $data['login_type']) : '';
            $return["login_type"] = $user_info['type'];
            $return["login_id"] = $user_info['id'];
            //记录日志
            $this->setLoginLog($user_info);
            //设置session信息
            $this->setLoginSession($user_info);
            return $this->resultArray('success', '登陆成功', $return);
        } catch (ProcessException $exception) {
            return $this->resultArray("failed", $exception->getMessage());
        }
    }

    /**
     * @param $user_info
     */
    public function setLoginSession($user_info)
    {
        Session::set('login_id', $user_info["id"], 'login');
        Session::set('login_type', $user_info["type"], 'login');
        Session::set('login_ip', $user_info["ip"], 'login');
        Session::set('login_type_name', $user_info["type_name"], 'login');
        Session::set('login_node_id', $user_info["node_id"], 'login');
    }

    /***
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function siteList()
    {
        $this->checkLogin();
        try {
            $site_model = new Site();
            $user_info = $this->getSessionUserInfo();
            $site_info = $site_model->where(['user_id' => $user_info["user_id"]])->select();
            if (!$site_info) {
                Common::processException('无网站');
            }
            $return = [];
            foreach ($site_info as $info) {
                $return[] = array('id' => $info['id'], 'url' => $info['url'], 'site_name' => $info['site_name']);
            }
            return $this->resultArray('success', '登陆成功', $return);
        } catch (ProcessException $exception) {
            return $this->resultArray("failed", $exception->getMessage());
        }

    }

    /**
     * 小网站用户存储站点信息
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function setSiteInfo()
    {
        $this->checkLogin();
        try {
            Session::clear('login_site');
            $site_id = $this->request->post("site_id");
            $rule = [
                ["site_id", "require", "请选择站点"],
            ];
            $validate = new Validate($rule);
            $data = $this->request->post();
            if (!$validate->check($data)) {
                Common::processException($validate->getError());
            }
            $site_info = (new Site)->where(["id" => $site_id])->find();
            if (!$site_info) {
                Common::processException('无此网站');
            }
            $this->setSiteSession($site_info);
            return $this->resultArray('success', '成功');
        } catch (ProcessException $exception) {
            return $this->resultArray("failed", $exception->getMessage());
        }
    }

    /**
     * @param $site_info
     */
    public function setSiteSession($site_info)
    {
        Session::set('site_id', $site_info["id"], 'login_site');
        Session::set('menu', array_unique(array_filter(explode(",", $site_info["menu"]))), 'login_site');
        Session::set('site_name', $site_info["site_name"], 'login_site');
    }

    /**
     * @param $user_info
     */
    public function setLoginLog($user_info)
    {
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
        $log['type'] = $user_info['type'];
        switch ($log['type']){
            case 1:
                $log['type_name'] = '系统管理员';
                break;
            case 2:
                $log['type_name'] = '节点管理员';
                break;
            case 3:
                $log['type_name'] = '站点后台';
                break;
        }
        $log['location'] = $location_info;
        LoginLog::create($log);
    }

    public function clearSession(){
        Session::clear('login');
        Session::clear('login_site');
    }
    public function getSession(){
        echo Session::get('login_id','login');
    }

    /**
     * 七天免登录验证
     * @return array
     * @throws \Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author jingzheng
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
                Common::processException($error);
            }
            //登录信息容器
            if ($data['login_type'] == 'node') {
                $user_info = (new User())->checkUserLogin($data["login_id"], $data["remember_key"], 'auto');
            } elseif ($data['login_type'] == 'site') {
                $user_info = (new SiteUser())->checkUserLogin($data["login_id"], $data["remember_key"], 'auto');
                $log['site_id'] = $user_info['id'];
            } else {
                Common::processException('未知错误');
            }
            // 获取ip信息
            $request = Request::instance();
            $ip = $request->ip();
            $user_info['ip'] = $ip;
            $user_info['type_name'] = $data['login_type'];
            //如果存在
            $return["remember_key"] = (isset($data['remember_key']) && $data['remember_key']) ? $this->getNewRememberStr($user_info['id'], $data['login_type']) : '';
            $return["login_type"] = $user_info['type'];
            $return["login_id"] = $user_info['id'];
            //设置session信息
            $this->setLoginSession($user_info);
            return $this->resultArray('success', '自动登陆成功', $return);
        } catch (ProcessException $exception) {
            return $this->resultArray("failed", $exception->getMessage());
        }
    }

    /***
     * 登出
     */
    public function logout()
    {
        Session::clear('login');
        Session::clear('login_site');
        return $this->resultArray('success', '登出成功');
    }


    /**
     * 调用resultArray方法
     * 返回json auth——name验证
     * 检测 0 无验证
     * @author jingzheng
     */

    //TODO oldfunction
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

    //TODO oldfunction
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
 