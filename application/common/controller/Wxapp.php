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
            Session::set('openid',$return['openid']);
            Session::set('session_key',$return['session_key']);
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
}