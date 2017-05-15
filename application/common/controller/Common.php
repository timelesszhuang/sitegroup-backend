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
        $user=$this->getSessionUser();
        if(empty($user_id)){
            exit(json_encode($this->resultArray('请先登录','failed')));
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
        $page=$this->request->get("page");
        $rows=$this->request->get("rows");
        if (intval($page) <1) {
            $page=1;
        }
        if (intval($rows) <1) {
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
        $module=$this->request->module();
        $arr=[];
        if($module=="sysadmin"){
            $arr["user_id"]=Session::get("sys_id");
            $arr["user_name"]=Session::get("sys_user_name");
            $arr["user_commpany_name"]=Session::get("sys_name");
            $arr["user_type"]=Session::get("sys_type");
            $arr["user_node_id"]=Session::get("sys_node_id");
        }else if($module=="admin"){
            $arr["user_id"]=Session::get("admin_id");
            $arr["user_name"]=Session::get("admin_user_name");
            $arr["user_commpany_name"]=Session::get("admin_name");
            $arr["user_type"]=Session::get("admin_type");
            $arr["user_node_id"]=Session::get("admin_node_id");
        }
        return $arr;
    }

    /**
     * 获取单条数据
     * @param $rescoure
     * @param $id
     * @return array
     */
    public function getread($rescoure,$id)
    {
        return $this->resultArray('', '', $rescoure->where(["id" => $id])->field("create_time,update_time",true)->find());
    }

    /**
     * 统一删除接口
     * @param $controller
     * @param $id
     * @author guozhen
     * @return array
     */
    public function deleteRecord($controller,$id)
    {
        $user=$this->getSessionUser();
        $where=[
            "id"=>$id,
            "node_id"=>$user["user_node_id"]
        ];
        if(!$controller->where($where)->delete()){
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
    public function publicUpdate($controller,$data,$id)
    {
        $user=$this->getSessionUser();
        $where=[
            "id"=>$id,
            "node_id"=>$user["user_node_id"]
        ];
        //前台可能会提交id过来,为了防止错误,所以将其删除掉
        if(array_key_exists('id',$data)){
            unset($data["id"]);
        }
        if (!$controller->where($where)->update($data)) {
            return $this->resultArray('修改失败', 'failed');
        }
        return $this->resultArray('修改成功');
    }
}
