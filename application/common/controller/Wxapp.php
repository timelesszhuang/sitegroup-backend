<?php
/**
 * Created by IntelliJ IDEA.
 * User: qiangbi
 * Date: 3/15/18
 * Time: 2:23 PM
 */

namespace app\common\controller;

use app\common\exception\ProcessException;
use think\Request;
use think\Session;
use think\Cookie;
use app\common\model\User;
use app\common\model\Node;
use think\Validate;

class Wxapp extends Common
{
    /***
     * @param Request $request
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function login(Request $request)
    {
        //https://api.weixin.qq.com/sns/jscode2session?appid=APPID&secret=SECRET&js_code=JSCODE&grant_type=authorization_code
        //小程序appid wxc9b6fb0ce74a27ce
        //小程序secret 602b3e81060f03915f3dab1e2b0573a0
        try {
            Session::clear('login');
            $code = $request->param('code');
            if (!$code) {
                Common::processException('参数错误');
            }
            $return = file_get_contents('https://api.weixin.qq.com/sns/jscode2session?appid=wxc9b6fb0ce74a27ce&secret=602b3e81060f03915f3dab1e2b0573a0&js_code='.$code.'&grant_type=authorization_code');
            $return = json_decode($return,true);
            if(array_key_exists('errcode',$return)){
                Common::processException(/*$return['errmsg']*/'参数错误');
            }
            Session::set('openid',$return['openid'],'wx');
            Session::set('session_key',$return['session_key'],'wx');
            $this->loginByOpenId($return['openid']);
            return $this->resultArray('success','登陆成功');
        } catch (ProcessException $e) {
            return $this->resultArray('failed', $e->getMessage());
        }
    }

    /**
     * @param $openid
     * @throws ProcessException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    static function loginByOpenId($openid){
        $user_info = (new User)->where(["openid" => $openid])->find();
        if(!$user_info){
            Common::processException('not_bind');
        }
        /** @var array $user_info */
        if ($user_info["id"] != 1) {
            // 查询node_id是否被禁用 如果被禁同样禁止登录
            $node_info = (new Node)->where(["id" => $user_info["node_id"]])->find();
            if (empty($node_info)) {
                Common::processException("当前用户没有节点后台!!");
            }
            if ($node_info["status"] == "off") {
                Common::processException("当前节点后台禁止登录!!");
            }
        }
        $user_info['type_name'] = 'node';
        $return_arr = [];
        /** @var array $user_info */
        $return_arr['id'] = $user_info['id'];
        $return_arr['node_id'] = $user_info['node_id'];
        $return_arr['type'] = $user_info['type'];
        $return_arr['type_name'] = $user_info['type_name'];
        $return_arr['ip'] = 'wxapp';
        //设置session信息
        (new Login())->setLoginSession($return_arr);
    }

    /***
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function bindopenid(){
        $data = Request::instance()->post();
        $rule = [
            ["user_name", "require", "请填写用户名"],
            ["password", "require", "请填写密码"],
        ];
        $validate = new Validate($rule);
        try {
            //验证字段
            if (!$validate->check($data)) {
                $error = $validate->getError();
                /** @var string $error */
                Common::processException($error);
            }
//            验证验证码
//            if (!captcha_check($data["verify_code"])) {
//                exception('验证码错误');
//            };
            //登录信息容器
            $user_info = (new User())->checkUserLogin($data["user_name"], $data["password"]);
            $user_info = (new User())->find($user_info['id']);
            $user_info->openid = Session::get('openid','wx');
            if(!$user_info->save()){
                Common::processException('绑定失败，请重新尝试');
            }
            //如果存在
            return $this->resultArray('success', '绑定成功');
        } catch (ProcessException $exception) {
            return $this->resultArray("failed", $exception->getMessage());
        }
    }
}