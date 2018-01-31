<?php
// +----------------------------------------------------------------------
// | Description: 基础类，无需验证权限。
// +----------------------------------------------------------------------
// | Author: timelesszhuang <834916321@qq.com>
// +----------------------------------------------------------------------

namespace app\common\controller;



class CommonLogin extends Common
{
    public function __construct()
    {
        parent::__construct();
        $this->checkLogin();
        $this->checkAuth();
    }
}
