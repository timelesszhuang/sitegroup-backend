<?php

namespace app\common\controller;

use think\Controller;
use think\Db;
use think\Request;

/** 相关用户的相关信息    包含用户登陆信息相关操作
 * Class UserInfo
 * @package app\common\controller
 */
class UserInfo extends Common
{

    /**
     * 获取用用户的相关信息
     * @author timelesszhuang
     */
    public function getUserInfo()
    {
        // 获取用户的相关信息
        $user_info = $this->getSessionUserInfo();
        $id = $user_info['user_id'];
        if ($user_info['user_type'] == 1) {
            // root 系统管理员
            $where['id'] = $id;
            $where['type'] = 1;
            $user = \app\common\model\User::where($where)->field('id,user_name,name,mobile,tel,email,wechat,qq,contacts')->find();
        } else if ($user_info['user_type'] == 2) {
            // node 管理员
            $where['id'] = $id;
            $where['type'] = 2;
            $user = \app\common\model\User::where($where)->field('id,user_name,name,mobile,tel,email,wechat,qq,contacts')->find();
        } else {
            //site 相关管理员
            $where['id'] = $id;
            $user = \app\common\model\SiteUser::where($where)->field('id,account,com_name,mobile,email')->find();
        }
        return $this->resultArray('success', '', $user);
    }

    /**
     * 获取用户的登录信息
     * @author timelesszhuang
     */
    public function getUserLoginList()
    {
        // 获取用户的相关信息
        $user_info = $this->getSessionUserInfo();
        if ($user_info['user_type'] == 1) {
            // root 系统管理员
            $where['type'] = 1;
            $where['node_id'] = $user_info['node_id'];
        } else if ($user_info['user_type'] == 2) {
            // node 管理员
            $where['type'] = 2;
            $where['node_id'] = $user_info['node_id'];
        } else {
            //site 相关管理员
            $where['type'] = 3;
            $where['site_id'] = $user_info['site_id'];
        }
        $limits = $this->getLimit();
        $data = Db::name('login_log')->where($where)->limit($limits['limit'], $limits['rows'])->order('id', 'desc')->field('create_time,ip,location')->select();
        $count = Db::name('login_log')->where($where)->count();
        foreach ($data as $k => $v) {
            $data[$k]['create_time'] = date('Y-m-d H:i', $v['create_time']);
        }
        return $this->resultArray(['rows' => $data, 'total' => $count]);
    }

}
