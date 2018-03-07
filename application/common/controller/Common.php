<?php
// +----------------------------------------------------------------------
// | Description: 基础类，无需验证权限。
// +----------------------------------------------------------------------
// | Author: timelesszhuang <834916321@qq.com>
// +----------------------------------------------------------------------

namespace app\common\controller;


use app\common\model\SystemConfig;
use app\common\model\SiteUser;
use app\common\model\User;
use think\Config;
use think\Controller;
use think\exception\HttpException;
use think\exception\HttpResponseException;
use think\Model;
use think\Request;
use think\Response;
use think\Session;
use app\common\exception\ProcessException;


class Common extends Controller
{
    protected $model;

    /**
     * 本地测试开启下 允许跨域ajax 获取数据
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取配置信息
     */
    //TODO oldfunction
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
     * 返回对象  默认不填为success
     * @param array $data
     * @param string $status
     * @param string $msg
     * @param string $detail
     * @return array
     * @author jingzheng
     */
    public function resultArray($status = 'success', $msg = '', $data = [], $detail = '')
    {
        if (is_array($status)) {
            $data = $status;
            $status = 'success';
            $msg = '';
        } else {
            if (($status != 'success') && ($status != 'failed') && ($status != 'logout') && ($status != 'noauth')) {
                $old_msg = $msg;
                $msg = $status;
                if ($old_msg == '') {
                    $status = 'success';
                } else {
                    if (is_array($old_msg)) {
                        $data = $old_msg;
                        $status = 'success';
                    } else {
                        $status = $old_msg;
                    }
                }
            }
        }
        return [
            'status' => $status,
            'data' => $data,
            'msg' => $msg,
            'detail' => $detail
        ];
    }

    /**
     * 获取用户相关的session信息
     * user_id 表示用户的id 也就是site_user表 或 user表的主键
     * user_type 表示用户的类型 1 表示 root 也就是公司管理员 2 表示node 相关管理员 3 表示user 小节点的管理员
     * user_type_name site站点 或 node节点
     * node_id 表示node_id
     * @return mixed
     */
    public function getSessionUserInfo()
    {
        $user_info_array['user_id'] = Session::get('login_id', 'login');
        $user_info_array['user_type'] = Session::get('login_type', 'login');
        $user_info_array['user_type_name'] = Session::get('login_type_name', 'login');
        $user_info_array['node_id'] = Session::get('login_node_id', 'login');
        if ($user_info_array['user_type_name'] == 'site') {
            $user_info_array['site_id'] = Session::get('site_id', 'login_site');
            $user_info_array['menu'] = Session::get('menu', 'login_site');
            $user_info_array['site_name'] = Session::get('site_name', 'login_site');
        }
        return $user_info_array;
    }

    /**
     * 获取配置列表
     * 重组数组
     * @author jingzheng
     */

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

    /**
     * 调用resultArray方法
     * 返回json auth??name验证
     * 检测 1 有验证
     * @author jingzheng
     */

    //TODO oldfunction
    public function getAuth()
    {
        $systemConfig = $this->getDataList(1);
        return $this->resultArray('', '', $systemConfig);
    }

    /***
     * @param $id
     * @param $salt
     * @return string
     */
    static public function getRememberStr($id, $salt)
    {
        $private = self::getCrypt();
        return md5($id . $salt . $private);
    }
    /***
     * @return string
     * 获取SYSTEM_CRYPT
     */
    static public function getCrypt(){
        $SystemConfig = new \app\common\model\SystemConfig();
        $auth_data = $SystemConfig->where(["name" => 'SYSTEM_CRYPT'])->find();
        return $auth_data['value'];
    }




    /***
     * @param $id
     * @param $option
     * @return string
     * @throws ProcessException
     */
    static public function getNewRememberStr($id, $option)
    {
        $private = Config::get("crypt.cookiePrivate");
        $salt = Common::getNewSale();
        $update['salt'] = $salt;
        $update['id'] = $id;
        if ($option == 'node') {
            (new User())->isUpdate(true)->save($update);
        } elseif ($option == 'site') {
            (new SiteUser())->isUpdate(true)->save($update);
        } else {
            Common::processException('未知错误');
        }
        return md5($id . $salt . $private);
    }

    /**
     * @return string
     */
    static public function getNewSale()
    {
        return chr(rand(97, 122)) . chr(rand(65, 90)) . chr(rand(97, 122)) . chr(rand(65, 90));
    }

    /**
     * 检查登录状态
     * @return void
     */
    public function checkLogin()
    {
        if (!Session::has('login_id', 'login') && !Session::has('login_type', 'login')) {
            exit(json_encode($this->resultArray('logout', '没有登录')));
        }
    }

    /**
     * 检查登录状态
     * @return void
     */
    public function checkAuth()
    {
        $request = Request::instance();
        $this_function = $request->module() . '/' . $request->controller() . '/' . $request->action();
        $auth_config = Config::get("auth");
        $user = $this->getSessionUserInfo();
        if (!(isset($auth_config[$this_function]) && in_array($user['user_type'], $auth_config[$this_function]))) {
            header('HTTP/1.1 403 Forbidden');
            exit(json_encode($this->resultArray('noauth', '没有权限' . $this_function)));
        }
    }


    /**
     * 抛出逻辑错误
     * @param $error
     * @param int $code
     * @throws ProcessException
     */
    static public function processException($error, $code = 0)
    {
        throw new ProcessException($error, $code);
    }

    /**
     * 获取limit
     * @return array
     * @author jingzheng
     */
    public function getLimit()
    {
        $page = $this->request->get("page");
        $rows = $this->request->get("rows");
        if (intval($page) < 1) {
            $page = 1;
        }
        if (intval($rows) < 1) {
            $rows = 10;
        }
        $limit = ($page - 1) * $rows;
        return ["limit" => $limit, "rows" => $rows];
    }

    /**
     * 获取单条数据
     * @param Model $model
     * @param $id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getread(Model $model, $id)
    {
        return $this->resultArray($model->where(["id" => $id])->field("create_time,update_time", true)->find()->toArray());
    }

    /**
     * 统一删除接口
     * @param $model
     * @param $id
     * @author jingzheng
     * @return array
     */
    public function deleteRecord(Model $model, $id)
    {
        $user = $this->getSessionUserInfo();
        $where = [
            "id" => $id,
            "node_id" => $user["node_id"]
        ];
        if (!$model->where($where)->delete()) {
            return $this->resultArray('failed', '删除失败');
        }
        return $this->resultArray('删除成功');
    }

    /**
     * 统一修改接口
     * @param Model $controller
     * @param $data
     * @param $id
     * @author jingzheng
     * @return array
     */
    public function publicUpdate($controller, $data, $id)
    {
        $user = $this->getSessionUserInfo();
        $where = [
            "id" => $id,
            "node_id" => $user["node_id"]
        ];
        //前台可能会提交id过来,为了防止错误,所以将其删除掉
        if (array_key_exists('id', $data)) {
            unset($data["id"]);
        }
        if (!$controller->where($where)->update($data)) {
            return $this->resultArray('failed', '修改失败');
        }
        return $this->resultArray('修改成功');
    }


    /**
     * 解压缩文件
     * @access public
     * @param $path 源文件的路径
     * @param $dest 解压缩到的路径
     * @return bool
     */
    //TODO oldfunction
    public function unzipFile($path, $dest)
    {
        $path = ROOT_PATH . 'public/' . $path;
        if (file_exists($path)) {
            //文件不存在
        }
//      $dest = 'upload/activity/activity/';
        $zip = new \ZipArchive;
        $res = $zip->open($path);
        if ($res === TRUE) {
            //解压缩到test文件夹
            $zip->extractTo($dest);
            $zip->close();
            return true;
        } else {
            return false;
        }
    }

    /**
     * 统一获取列表
     * @param $model
     * @param $field
     * @return array
     */
    //TODO oldfunction
    public function getList($model, $field)
    {
        $user_info = $this->getSessionUserInfo();
        $where["node_id"] = [["=", $user_info["node_id"]], ["=", 0], "or"];
        $data = $model->field($field)->where($where)->select();
        array_walk($data, [$this, "formatter_data"]);
        return $this->resultArray('', '', $data);
    }

    /**
     * 格式化数据
     * @param $v
     * @param $k
     */
    //TODO oldfunction
    public function formatter_data($v, $k)
    {
        if (isset($v["node_id"]) && ($v["node_id"] == 0)) {
            $v["text"] = $v["text"] . "?" . $v["industry_name"] . "?公共模板";
        } else {
            if (isset($v["industry_name"]) && isset($v["text"])) {
                $v["text"] = $v["text"] . "?专属";
            }
        }
    }

    /**
     * 获取小站点的session
     * @param $item
     * @return mixed
     */
    //TODO oldfunction
    public function getSiteSession($item)
    {
        $arr = Session::get($item);
        return $arr;
    }

    /**
     * 匹配http
     * @param $http
     * @return string
     */
    //TODO oldfunction
    public function searchHttp($http)
    {
        return strrchr("http", $http);
    }


    /**
     * 截取中文字符串 会过滤出数据 utf-8
     * @param String $str 要截取的中文字符串
     * @param $len
     * @return
     */
    //TODO oldfunction
    public function utf8chstringsubstr($str, $len)
    {
        $str = strip_tags($str);
        for ($i = 0; $i < $len; $i++) {
            $temp_str = substr($str, 0, 1);
            if (ord($temp_str) > 127) {
                $i++;
                if ($i < $len) {
                    $new_str[] = substr($str, 0, 3);
                    $str = substr($str, 3);
                }
            } else {
                $new_str[] = substr($str, 0, 1);
                $str = substr($str, 1);
            }
        }
        //把数组元素组合为string4 - 5 - 6
        return join($new_str);
    }


    public function test()
    {
        $str = "aaaa\n中";
        echo strlen($str);
    }

}
