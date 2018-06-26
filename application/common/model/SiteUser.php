<?php

namespace app\common\model;


use app\common\model\Site;
use app\common\controller\Common;
use app\common\model\LoginLog;
use think\Config;
use think\Model;
use think\Request;
use think\Session;
use app\common\traits\Obtrait;

class SiteUser extends Model
{
    use Obtrait;

    //只读字段
    protected $readonly = ["node_id"];

    //初始化操作
    public static function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
        //写入事件
        SiteUser::event('before_insert', function ($siteuser) {
            $siteuser->pwd = md5($siteuser->pwd . $siteuser->account);
            $siteuser->salt = chr(rand(97, 122)) . chr(rand(65, 90)) . chr(rand(97, 122)) . chr(rand(65, 90));
            $node = Node::get($siteuser->node_id);
            $siteuser->com_name = $node->com_name;
        });
        //修改事件
        SiteUser::event('before_update', function ($siteuser) {
            if (isset($siteuser->pwd)) {
                $siteuser->pwd = md5($siteuser->pwd . $siteuser->account);
            }
        });
    }

    /**
     * 用户登录验证
     * @param $username_or_user_id
     * @param $pwd_or_remenber_key
     * @param string $option
     * @return array
     * @throws \Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author jingyang
     */
    public function checkUserLogin($username_or_user_id, $pwd_or_remenber_key, $option = 'login')
    {
        if ($option == 'login') {
            $user_info = self::where(["account" => $username_or_user_id])->find();
            if (empty($user_info)) {
                Common::processException("用户名错误");
            }
            $user_info_arr = $user_info->toArray();
            if (md5($pwd_or_remenber_key . $username_or_user_id) != $user_info_arr["pwd"]) {
                Common::processException("用户名或密码错误");
            }
        } elseif ($option == 'auto') {
            $user_info = self::where(["id" => $username_or_user_id])->find();
            if (empty($user_info)) {
                Common::processException('用户名错误');
            }
            $user_info_arr = $user_info->toArray();
            if (Common::getRememberStr($user_info_arr['id'], $user_info_arr['salt']) != $pwd_or_remenber_key) {
                Common::processException('用户登录数据错误');
            }
        }
        // 查询当前用户是否被禁止登录
        /** @var array $user_info_arr */
        if ($user_info_arr['is_on'] == 20) {
            Common::processException("当前用户被禁止登录!!");
        }
        // 查询node_id是否被禁用 如果被禁同样禁止登录
        $node_info = (new Node)->where(["id" => $user_info_arr["node_id"]])->find();
        if (empty($node_info)) {
            Common::processException("当前用户没有节点后台!!");
        }
        if ($node_info["status"] == "off") {
            Common::processException("当前节点后台禁止登录!!");
        }
        $return_arr = [];
        /** @var array $user_info */
        $return_arr['id'] = $user_info['id'];
        $return_arr['node_id'] = $user_info['node_id'];
        $return_arr['name'] = $user_info['name'];
        $return_arr['salt'] = $user_info['salt'];
        $return_arr['type'] = 3;
        $return_arr['type_name'] = '站点后台';
        $return_arr['site_id'] = $user_info['id'];;
        return $return_arr;
    }

    /**
     * 获取登录网站信息
     * @param $user_id
     * @return false|\PDOStatement|string|\think\Collection
     */
    //TODO oldfunction
    public function getSiteInfo($user_id)
    {
        $siteInfo = Site::where(["user_id" => $user_id])->field("id,url,site_name")->select();
        return $siteInfo;
    }

    /**
     * 获取所有数据
     * @param $limit
     * @param $rows
     * @param $where
     * @return array
     * @author guozhen
     */
    //TODO oldfunction
    public function getAll($limit, $rows, $where)
    {
        $count = $this->where($where)->count();
        $data = $this->limit($limit, $rows)->where($where)->field('update_time', true)->order('id', 'desc')->select();
        return [
            "total" => $count,
            "rows" => $data
        ];

    }

}
