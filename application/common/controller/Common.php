<?php
// +----------------------------------------------------------------------
// | Description: 基础类，无需验证权限。
// +----------------------------------------------------------------------
// | Author: timelesszhuang <834916321@qq.com>
// +----------------------------------------------------------------------

namespace app\common\controller;


use app\admin\model\SystemConfig;
use app\common\model\User;
use think\Controller;
use think\Session;
use think\Validate;


class Common extends Controller
{

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
        $this->checkSession();
    }

    /**
     * 获取配置信息
     */
    function getConfigInfo()
    {
        $systemConfig = cache('DB_CONFIG_DATA');
        if (!$systemConfig) {
            //获取所有系统配置
            $systemConfig = (new SystemConfig())->getDataList();
            cache('DB_CONFIG_DATA', null);
            cache('DB_CONFIG_DATA', $systemConfig, 36000); //缓存配置
        }
        return $this->resultArray(['data' => $systemConfig]);
    }

    /**
     * 获取 验证码测试
     * @access public
     */
    public function getCaptcha()
    {
        //captcha_check()
        print_r(Session::get('name'));
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
     * 获取配置列表
     * 重组数组
     * @auther jingzheng
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

    /**
     * 调用resultArray方法
     * 返回json auth——name验证
     * 检测 1 有验证
     * @auther jingzheng
     */

    public function getAuth()
    {
        $systemConfig = $this->getDataList(1);
        return $this->resultArray('', '', $systemConfig);
    }

    /**
     * 修改密码
     * @return array|void
     */
    public function changePwd()
    {
        $rule = [
            ["oldPwd", "require", "请输入原始密码"],
            ["newPwd", "require", "请输入新密码"]
        ];
        $post = $this->request->post();
        $validate = new Validate($rule);
        if (!$validate->check($post)) {
            return $this->resultArray($validate->getError(), "failed");
        }
        $user_info = (new User)->changePwd($post["oldPwd"], $post['newPwd']);
        return $this->resultArray($user_info[0], $user_info[1], $user_info[2]);
    }

    /**
     * 检查session
     */
    public function checkSession()
    {
        $user_id=Session::get("type");
        if(empty($user_id)){
            exit(json_encode($this->resultArray('请先登录','failed')));
        }
    }

    /**
     * 获取limit
     * @param $page
     * @param $rows
     * @return int
     */
    public function getLimit()
    {
        $page=$this->request->get("page");
        $rows=$this->request->get("rows");
        if ($page <1) {
            $page=1;
        } else if ($rows <1 || $rows>20) {
            $rows=10;
        }
        $limit=($page-1)*$rows;
        return ["limit"=>$limit,"rows"=>$rows];
    }

    /**
     * 获取前后台用户统一session信息
     * @return array
     */
    public function getSessionUser()
    {
        $type=Session::get("type");
        $arr=[];
        if($type==1){
            $arr["user_id"]=Session::get("sys_id");
            $arr["user_name"]=Session::get("sys_user_name");
            $arr["user_commpany_name"]=Session::get("sys_name");
        }else if($type==2){
            $arr["user_id"]=Session::get("admin_id");
            $arr["user_name"]=Session::get("admin_user_name");
            $arr["user_commpany_name"]=Session::get("admin_name");
        }
        return $arr;
    }
}