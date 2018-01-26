<?php
/**
 * Created by IntelliJ IDEA.
 * User: qiangbi
 * Date: 1/25/18
 * Time: 5:15 PM
 */

namespace app\user\controller;

use app\common\controller\Common;

class User extends Common
{
    public function __construct()
    {
        $this->checkSession();
        parent::__construct();
    }
}