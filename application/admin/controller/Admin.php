<?php
/**
 * Created by IntelliJ IDEA.
 * User: qiangbi
 * Date: 1/25/18
 * Time: 4:56 PM
 */

namespace app\admin\controller;

use app\common\controller\Common;

class Admin extends Common
{
    public function __construct()
    {
        $this->checkSession();
        parent::__construct();
    }
}