<?php
/**
 * Created by IntelliJ IDEA.
 * User: qiangbi
 * Date: 1/26/18
 * Time: 6:34 PM
 */

namespace app\common\controller;


use app\common\exception\ProcessException;
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
    public function change_password(){
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
            } elseif ($login_user_info['user_type_name'] == 'site') {
                $model = (new SiteUser());
            } else {
                Common::processException('未知错误');
            }
            /** @var \think\model $model */
            $obj = $model->get($login_user_info['user_id']);
            if($obj->pwd != $data['old_password']){
                Common::processException('密码验证错误');
            }
            $obj->pwd = $data['new_password'];
            $obj->save();
            return $this->resultArray();
        } catch (ProcessException $exception) {
            return $this->resultArray("failed", $exception->getMessage());
        }
    }
}