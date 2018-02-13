<?php
// +----------------------------------------------------------------------
// | Description: 基础类，无需验证权限。
// +----------------------------------------------------------------------
// | Author: timelesszhuang <834916321@qq.com>
// +----------------------------------------------------------------------

namespace app\common\controller;


use app\common\exception\ProcessException;
use app\common\model\CaseCenter;
use app\common\model\Company;
use app\common\model\CountData;
use app\common\model\LoginLog;
use app\common\model\Marketingmode;
use app\common\model\Node;
use app\common\model\SiteUser;
use app\common\model\User;
use app\common\controller\Count;

class Home extends CommonLogin
{
    /**
     * 首页统计
     */
    public function countDatas()
    {
        $user = $this->getSessionUserInfo();
        $ttime = strtotime(date("Y-m-d 00:00:00"));
        $cd = new CountData();
        return $this->resultArray([
            "pv" => intval($cd->countPv($user["node_id"], $ttime)),
            "useragent" => intval($cd->countUseragent($user["node_id"], $ttime)),
            "article" => intval($cd->countArticle($user["node_id"], $ttime)),
            "shuaidan" => intval($cd->countShuaidan($user["node_id"], $ttime)),
            "shoulu" => intval($cd->countInclude($user["node_id"]))
        ]);
    }

    /**
     * root首页统计
     */
    public function RootCountDatas()
    {
        $ttime = strtotime(date("Y-m-d 00:00:00"));
        $cd = new CountData();
        return $this->resultArray([
            "site_num" => intval($cd->countSite()),
            "customer_num" => intval($cd->countCustomer()),
            "new_article" => intval($cd->countArticle(0, $ttime)),
            "shoulu" => intval($cd->countInclude())
        ]);
    }

    /***
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getLanderInfo()
    {
        try {
            $node_model = (new Node());
            $company_model = (new Company());
            $user = $this->getSessionUserInfo();
            $return = [];
            $return['user_id'] = $user['user_id'];
            $return['user_type_name'] = $user['user_type_name'];
            $return['user_name'] = $user['user_id'];
            $node_info = $node_model->get($user['node_id']);
            $company_info = $company_model->get($node_info['com_id']);
            if ($user['user_type_name'] == 'node') {
                $model = (new User());
                $user_info = $model->get($user['user_id']);
                $return['user_name'] = $user_info['user_name'];
                $return['info_status'] = $company_info['is_checked'];
            } elseif ($user['user_type_name'] == 'site') {
                $model = (new SiteUser());
                $user_info = $model->get($user['user_id']);
                $return['user_name'] = $user_info['name'];
            } else {
                Common::processException('未知错误');
            }
            $return['com_id'] = $company_info['id'];
            $return['com_name'] = $company_info['name'];
            $last_login_info = (new LoginLog())->lastLoginInfo();
            $return['last_login_ip'] = '无';
            $return['last_login_time'] = '无';
            $return['last_login_address'] = '无';
            if ($last_login_info) {
                $return['last_login_ip'] = $last_login_info['ip'];
                $return['last_login_time'] = $last_login_info['create_time'];
                $return['last_login_address'] = $last_login_info['location'];
            }
            return $this->resultArray('获取成功', $return);
        } catch (ProcessException $e) {
            return $this->resultArray('failed', $e->getMessage());
        }
    }


    /**
     * 获取前4条数据给前台
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getFour()
    {
        $data['marketingmode'] = (new Marketingmode())->limit(4)->order("id", "desc")->field("id,img,title,create_time")->select();
        $data['case_center'] = (new CaseCenter())->limit(4)->order("id", "desc")->field("id,title,create_time")->select();
        return $this->resultArray($data);
    }

    /**
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function show(){
        return (new Count)->show();
    }

    /**
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function en(){
        return (new Count)->en();
    }
}
