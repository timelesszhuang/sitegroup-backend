<?php
// +----------------------------------------------------------------------
// | Description: 基础类，无需验证权限。
// +----------------------------------------------------------------------
// | Author: timelesszhuang <834916321@qq.com>
// +----------------------------------------------------------------------

namespace app\common\controller;


use app\admin\model\Articletype;
use app\admin\model\Contactway;
use app\admin\model\Domain;
use app\admin\model\Keyword;
use app\admin\model\Menu;
use app\admin\model\Site;
use app\admin\model\SiteType;
use app\admin\model\SiteUser;
use app\admin\model\TypeTag;
use app\admin\model\SystemConfig;
use app\common\model\User;
use think\Controller;
use think\Db;
use think\Request;
use think\Session;
use think\Validate;
use app\admin\model\Menutag;

class Common extends Controller
{


    /**
     * @return array
     */
    public function add_page()
    {
        try {
            Db::startTrans();
            $post = $this->request->post();
            $yuming = $post['yuming'];
            $gongsiming = $post['gongsiming'];
            $gongsiyingwenming = $post['gongsiyingwenming'];
            $gongsijianjie = $post['gongsijianjie'];

            //注册用户相关信息
            $name = $gongsiming;
            $username = $yuming;
            $email = $post['email'];
            $phone = $post['phone'];
            $pwd = 'admin123';
            //content
            $content_zipcode = $post['content_zipcode'];
            $content_fax = $post['content_fax'];
            $content_telephone = $post['content_telephone'];
            $content_weixin = $post['content_weixin'];
            $content_email = $post['content_email'];
            $content_mobile = $post['content_mobile'];
            $content_four00 = $post['content_four00'];
            $content_qq = $post['content_qq'];
            $content_address = $post['content_address'];
            $user = $this->getSessionUser();
            //添加文章分类标签
            $Type_Tag = new TypeTag();
            $tag_where['node_id'] = $user['user_node_id'];
            $tag_where['tag'] = $gongsiming;
            $typetag = $Type_Tag->where($tag_where)->find();
            if ($typetag) {
                $tag_id = $typetag['id'];
            } else {
                $data_tag['tag'] = $gongsiming;
                $data_tag['node_id'] = $user['user_node_id'];
                if (!$Type_Tag->create($data_tag)) {
                    exception("标签创建失败");
                }
                $tag_id = $Type_Tag->getLastInsID();
            }
            //添加文章分类
            $Articletype = new Articletype();
            $data_art_type['tag_id'] = $tag_id;
            $data_art_type['name'] = '公司资讯';
            $data_art_type['detail'] = $gongsiming;
            $data_art_type['alias'] = 'gongsizixun' . time();
            $data_art_type['node_id'] = $user['user_node_id'];
            if (!$Articletype->create($data_art_type)) {
                exception("类型创建失败");
            }
            $art_type_id = $Articletype->getLastInsID();
            //添加栏目标签
            $Menutag = new Menutag();
            $data_menu_tag['node_id'] = $user['user_node_id'];
            $data_menu_tag['name'] = $gongsiming;
            $data_menu_tag['detail'] = $gongsiming;
            if (!$Menutag->create($data_menu_tag)) {
                exception("Menutag创建失败");
            }
            $menu_tag_id = $Menutag->getLastInsID();
            //添加栏目
            $Menu = new Menu();
            $data_menu['node_id'] = $user['user_node_id'];
            $data_menu['name'] = '公司资讯';
            $data_menu['generate_name'] = 'gongsizixun' . time();
            $data_menu['title'] = '公司资讯';
            $data_menu['flag'] = 3;
            $data_menu['flag_name'] = '文章型';
            $data_menu['content'] = '';
            $data_menu['type_id'] = $art_type_id;
            $data_menu['tag_id'] = $menu_tag_id;
            $data_menu['tag_name'] = $gongsiming;
            if (!$Menu->create($data_menu)) {
                exception("公司资讯创建失败");
            }
            $menu_id1 = $Menu->getLastInsID();
            $data_menu['name'] = '关于我们';
            $data_menu['title'] = '关于我们';
            $data_menu['flag'] = 1;
            $data_menu['generate_name'] = 'guanyuwomen' . time();
            $data_menu['flag_name'] = '详情型';
            $data_menu['content'] = $gongsijianjie;
            $data_menu['type_id'] = '';
            $data_menu['covertemplate'] = 'guanyuwomen.html';
            if (!$Menu->create($data_menu)) {
                exception("关于我们创建失败");
            }
            $menu_id2 = $Menu->getLastInsID();
            //网站分类
            $site_type = new SiteType();
            $data_site_type['node_id'] = $user['user_node_id'];
            $data_site_type['name'] = $gongsiming;
            $data_site_type['detail'] = $gongsiming;
            $data_site_type['chain_type'] = 10;
            if (!$site_type->create($data_site_type)) {
                exception("site_type创建失败");
            }
            $site_type_id = $site_type->getLastInsID();
            //用户名
            $site_user = new SiteUser();
            $data_site_user['name'] = $name;
            $data_site_user['pwd'] = $pwd;
            $data_site_user['email'] = $email;
            $data_site_user['mobile'] = $phone;
            $data_site_user['account'] = $username;
            $data_site_user['node_id'] = $user['user_node_id'];
            if (!$site_user->create($data_site_user)) {
                exception("公司用户失败");
            }
            $site_user_id = $site_user->getLastInsID();
            //添加域名
            $domain = new Domain();
            $data_domain['domain'] = $yuming;
            $data_domain['node_id'] = $user['user_node_id'];
            if (!$domain->create($data_domain)) {
                exception("公司用户失败");
            }
            $domain_id = $domain->getLastInsID();
            //联系方式
            $content = new Contactway();
            $data_content['name'] = $gongsiming;
            $data_content['detail'] = $gongsiming;
            $data_content['html']['zipcode'] = $content_zipcode;
            $data_content['html']['fax'] = $content_fax;
            $data_content['html']['telephone'] = $content_telephone;
            $data_content['html']['weixin'] = $content_weixin;
            $data_content['html']['email'] = $content_email;
            $data_content['html']['mobile'] = $content_mobile;
            $data_content['html']['four00'] = $content_four00;
            $data_content['html']['qq'] = $content_qq;
            $data_content['html']['address'] = $content_address;
            $data_content['node_id'] = $user['user_node_id'];
            if (!$content->create($data_content)) {
                exception("添加联系方式失败");
            }
            $content_id = $content->getLastInsID();
            //关键词
            $keyword = new Keyword();
            $data_keyword['name'] = $gongsiming;
            $data_keyword['node_id'] = $user['user_node_id'];
            $data_keyword['parent_id'] = 0;
            $data_keyword['path'] = '';
            if (!$keyword->create($data_keyword)) {
                exception("添加关键词失败");
            }
            $keyword_id_A = $keyword->getLastInsID();
            $data_keyword['parent_id'] = $keyword_id_A;
            $data_keyword['path'] = ",$keyword_id_A,";
            if (!$keyword->create($data_keyword)) {
                exception("添加关键词失败");
            }
            $keyword_id_B = $keyword->getLastInsID();
            $data_keyword['parent_id'] = $keyword_id_B;
            $data_keyword['path'] = ",$keyword_id_A,$keyword_id_B,";
            if (!$keyword->create($data_keyword)) {
                exception("添加关键词失败");
            }
            //添加站点
            $site = new Site();

            $data_site['site_name'] = $gongsiming;
            $data_site['com_name'] = $gongsiming;
            $data_site['is_mobile'] = 10;
            $data_site['url'] = "http://".$yuming;
            $data_site['domain_id'] = $domain_id;
            $data_site['domain'] = $yuming;
            $data_site['user_id'] = $site_user_id;
            $data_site['user_name'] = $name;
            $data_site['menu'] = ",$menu_id1,$menu_id2,";
            $data_site['keyword_ids'] = ",$keyword_id_A,";
            $data_site['site_type'] = $site_type_id;
            $data_site['site_type_name'] = $data_site_type['name'];
            $data_site['template_id'] = 78;
            $data_site['support_hotline'] = $content_id;
            $data_site["node_id"] = $user['user_node_id'];
            $site->create($data_site);
            Db::commit();
//            Db::rollback();
            return $this->resultArray('成功');
        } catch (\Exception $exception) {
            Db::rollback();
            return $this->resultArray($exception->getMessage(), "failed");
        }
    }


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
        if (empty($stat) || $stat == 'success') {
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
        $where["node_id"] = [["=", $user["user_node_id"]], ["=", 0], "or"];
        $data = $model->field($field)->where($where)->select();
        array_walk($data, [$this, "formatter_data"]);
        return $this->resultArray('', '', $data);
    }

    /**
     * 格式化数据
     * @param $v
     * @param $k
     */
    public function formatter_data($v, $k)
    {
        if (isset($v["node_id"]) && ($v["node_id"] == 0)) {
            $v["text"] = $v["text"] . "—" . $v["industry_name"] . "—公共模板";
        } else {
            if (isset($v["industry_name"]) && isset($v["text"])) {
                $v["text"] = $v["text"] . "—专属";
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
