<?php
/**
 * Created by PhpStorm.
 * User: qiangbi
 * Date: 17-4-26
 * Time: 下午2:25
 */

namespace app\common\model;

use app\common\controller\Common;
use app\common\traits\Osstrait;
use think\Config;
use think\Model;

class TypeTag extends Model
{
    use Osstrait;
    //只读字段
    protected $readonly = ["node_id"];

    /**
     * 获取所有 图集
     * @param $limit
     * @param $rows
     * @param int $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    //TODO oldfunction
    public function getList($limit, $rows, $where = 0)
    {
        $count = $this->where($where)->count();
        $data = $this->limit($limit, $rows)->where($where)->field('content,summary,update_time,readcount,title_color', true)->order('id desc')->select();
        return [
            "total" => $count,
            "rows" => $data
        ];
    }

    /**
     * @param $tag_name
     * @return mixed|string
     * @throws \app\common\exception\ProcessException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getTagIdByName($tag_name)
    {
        $user = (new Common)->getSessionUserInfo();
        $where1['node_id'] = $user['node_id'];
        $where1['tag'] = $tag_name;
        $Type_Tag = new TypeTag();
        $typetag = $Type_Tag->where($where1)->find();
        if ($typetag) {
            $tag_id = $typetag['id'];
        } else {
            $data_tag['tag'] = $tag_name;
            $data_tag['node_id'] = $user['node_id'];
            if (!$Type_Tag::create($data_tag)) {
                Common::processException('标签创建失败');
            }
            $tag_id = $Type_Tag->getLastInsID();
        }
        return $tag_id;
    }

}