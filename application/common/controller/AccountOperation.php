<?php
/**
 * Created by IntelliJ IDEA.
 * User: jingyang
 * Date: 1/26/18
 * Time: 6:34 PM
 */

namespace app\common\controller;


use app\common\exception\ProcessException;
use app\common\model\Company;
use app\common\model\LoginLog;
use app\common\model\Node;
use think\Request;
use think\Validate;
use app\common\model\SiteUser;
use app\common\model\User;

class AccountOperation extends CommonLogin
{
    /**
     * @return array
     * @throws \think\exception\DbException
     */
    public function changePassword()
    {
        $data = Request::instance()->post();
        $rule = [
            ["old_password", "require", "请填写原始密码"],
            ["new_password", "require", "请填写新密码"],
        ];
        $login_user_info = $this->getSessionUserInfo();
        $validate = new Validate($rule);
        try {
            //验证字段
            if (!$validate->check($data)) {
                $error = $validate->getError();
                /** @var string $error */
                Common::processException($error);
            }
            if ($login_user_info['user_type_name'] == 'node') {
                $model = (new User());
                $obj = $model->get($login_user_info['user_id']);
                if (isset($obj->pwd)) {
                    if (isset($obj->user_name)) {
                        if ($obj->pwd != md5($data['old_password'] . $obj->user_name)) {
                            Common::processException('密码验证错误');
                        }
                    } else {
                        Common::processException('密码验证错误');
                    }
                } else {
                    Common::processException('密码验证错误');
                }
            } elseif ($login_user_info['user_type_name'] == 'site') {
                $model = (new SiteUser());
                $obj = $model->get($login_user_info['user_id']);
                if (isset($obj->pwd)) {
                    if (isset($obj->account)) {
                        if ($obj->pwd != md5($data['old_password'] . $obj->account)) {
                            Common::processException('密码验证错误');
                        }
                    } else {
                        Common::processException('密码验证错误');
                    }
                } else {
                    Common::processException('密码验证错误');
                }
            } else {
                Common::processException('未知错误');
            }
            /** @var \stdClass $obj */
            $obj->pwd = $data['new_password'];
            $obj->save();
            return $this->resultArray();
        } catch (ProcessException $exception) {
            return $this->resultArray("failed", $exception->getMessage());
        }
    }

    /***
     * @return array
     * @throws \think\exception\DbException
     */
    public function getLanderInfo()
    {
        try {
            $user = $this->getSessionUserInfo();
            $return = [];
            $return['user_id'] = $user['user_id'];
            $return['user_type_name'] = $user['user_type_name'];
            $return['user_name'] = $user['user_id'];
            if ($user['user_type_name'] == 'node') {
                $model = (new User());
                $node_model = (new Node());
                $company_model = (new Company());
                $user_info = $model->get($user['user_id']);
                $node_info = $node_model->get($user_info['node_id']);
                $company_info = $company_model->get($node_info['com_id']);
                $return['user_name'] = $user_info['name'];
                $return['com_id'] = $company_info['id'];
                $return['com_name'] = $company_info['name'];
                $return['info_status'] = $company_info['is_checked'];
            } elseif ($user['user_type_name'] == 'site') {
                $model = (new SiteUser());
                $user_info = $model->get($user['user_id']);
                $return['user_name'] = $user_info['name'];
            } else {
                Common::processException('未知错误');
            }
            $last_login_info = (new LoginLog())->lastLoginInfo();
            $return['last_login_ip']='无';
            $return['last_login_time']='无';
            $return['last_login_address']='无';
            if($last_login_info){
                $return['last_login_ip']=$last_login_info['ip'];
                $return['last_login_time']=$last_login_info['create_time'];
                $return['last_login_address']=$last_login_info['location'];
            }
            return $this->resultArray('获取成功',$return);
        } catch (ProcessException $e) {
            return $this->resultArray('failed', $e->getMessage());
        }
    }
}