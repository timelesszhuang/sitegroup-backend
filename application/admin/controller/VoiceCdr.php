<?php

namespace app\admin\controller;

use think\Config;
use think\Request;
use app\common\controller\Common;
use think\Validate;
use app\common\model\VoiceCdr as model;

class VoiceCdr extends Common
{
    use Obtrait;
    use Osstrait;

    /**
     * 显示资源列表
     *
     * @param Request $request
     * @return array
     * @author guozhen
     */
    public function index(Request $request)
    {
        $limits = $this->getLimit();
        $where = [];
        $user = $this->getSessionUser();
        $where["node_id"] = $user["user_node_id"];
        return $this->resultArray('', '', (new model())->getAll($limits['limit'], $limits['rows'], $where));
    }
}
