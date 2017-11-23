<?php
// +----------------------------------------------------------------------
// | Description: 基础类，无需验证权限。
// +----------------------------------------------------------------------
// | Author: timelesszhuang <834916321@qq.com>
// +----------------------------------------------------------------------

namespace app\common\controller;


use app\admin\model\Site;
use app\admin\model\SystemConfig;
use app\common\model\SiteErrorInfo;
use app\common\model\User;
use think\Controller;
use think\Request;
use think\Session;
use think\Validate;


class Common extends Controller
{

    /**
     * 本地测试开启下 允许跨域ajax 获取数据
     */
    public function __construct()
    {
        $this->checkSession();
        parent::__construct();
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
     * @author guozhen
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

    /**
     * 调用resultArray方法
     * 返回json auth——name验证
     * 检测 1 有验证
     * @author jingzheng
     */

    public function getAuth()
    {
        $systemConfig = $this->getDataList(1);
        return $this->resultArray('', '', $systemConfig);
    }

    /**
     * 修改密码
     * @return array|void
     * @author guozhen
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
        $user = $this->getSessionUser();
        if (empty($user["user_id"])) {
            exit(json_encode(
                [
                    'status' => "loginout",
                    'data' => '',
                    'msg' => "请先登录"
                ]
            ));
        }
    }

    /**
     * 获取limit
     * @param $page
     * @param $rows
     * @return int
     * @author guozhen
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
     * 获取前后台用户统一session信息
     * @author guozhen
     * @return array
     */
    public function getSessionUser()
    {
        $request = Request::instance();
        $module = $request->module();
        $arr = [];
        switch ($module) {
            //大后台
            case "common":
            case "sysadmin":
                $arr["user_id"] = Session::get("sys_id");
                $arr["user_name"] = Session::get("sys_user_name");
                $arr["user_commpany_name"] = Session::get("sys_name");
                $arr["user_type"] = Session::get("sys_type");
                $arr["user_node_id"] = Session::get("sys_node_id");
                break;
            //节点后台
            case "admin":
                $arr["user_id"] = Session::get("admin_id");
                $arr["user_name"] = Session::get("admin_user_name");
                $arr["user_commpany_name"] = Session::get("admin_name");
                $arr["user_type"] = Session::get("admin_type");
                $arr["user_node_id"] = Session::get("admin_node_id");
                break;
            //站点后台
            case "user":
                $arr["user_id"] = Session::get("login_site")["id"];
                $arr["user_name"] = Session::get("login_site")["name"];
                $arr["user_node_id"] = Session::get("login_site")["node_id"];
                break;
        }
        return $arr;
    }

    /**
     * 获取单条数据
     * @param $rescoure
     * @param $id
     * @return array
     */
    public function getread($rescoure, $id)
    {
        return $this->resultArray('', '', $rescoure->where(["id" => $id])->field("create_time,update_time", true)->find());
    }

    /**
     * 统一删除接口
     * @param $controller
     * @param $id
     * @author guozhen
     * @return array
     */
    public function deleteRecord($controller, $id)
    {
        $user = $this->getSessionUser();
        $where = [
            "id" => $id,
            "node_id" => $user["user_node_id"]
        ];
        if (!$controller->where($where)->delete()) {
            return $this->resultArray('删除失败', 'failed');
        }
        return $this->resultArray('删除成功');
    }

    /**
     * 统一修改接口
     * @param $controller
     * @param $data
     * @param $id
     * @author guozhen
     * @return array
     */
    public function publicUpdate($controller, $data, $id)
    {
        $user = $this->getSessionUser();
        $where = [
            "id" => $id,
            "node_id" => $user["user_node_id"]
        ];
        //前台可能会提交id过来,为了防止错误,所以将其删除掉
        if (array_key_exists('id', $data)) {
            unset($data["id"]);
        }
        if (!$controller->where($where)->update($data)) {
            return $this->resultArray('修改失败', 'failed');
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
    public function getList($model, $field)
    {
        $user = $this->getSessionUser();
        $where["node_id"] = [["=",$user["user_node_id"]],["=",0],"or"];
        $data = $model->field($field)->where($where)->select();
        array_walk($data,[$this,"formatter_data"]);
        return $this->resultArray('', '', $data);
    }

    /**
     * 格式化数据
     * @param $v
     * @param $k
     */
    public function formatter_data($v,$k)
    {
        if(isset($v["node_id"]) && ($v["node_id"]==0)){
            $v["text"]=$v["text"]."—".$v["industry_name"]."—公共模板";
        }else{
            if(isset($v["industry_name"]) && isset($v["text"])){
                $v["text"]=$v["text"]."—专属";
            }
        }
    }

    /**
     * 获取小站点的session
     * @param $item
     * @return mixed
     */
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
        //把数组元素组合为string
        return join($new_str);
    }

}
