<?php
/**
 * Created by PhpStorm.
 * User: qiangbi
 * Date: 17-4-26
 * Time: 下午2:25
 */

namespace app\common\model;
use app\common\controller\Common;
use think\db\exception\DataNotFoundException;
use think\Model;

class Articletype extends Model
{
    //只读字段
    protected $readonly=["node_id"];

    /**
     * @param $limit
     * @param $rows
     * @param int $where
     * @return array
     * @throws DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getArticletype($limit,$rows,$where=0)
    {
        $count=$this->where($where)->count();
        $data=$this->limit($limit,$rows)->where($where)->order('id desc')->select();
        return [
            "total"=>$count,
            "rows"=>$data
        ];
    }

    /**
     * @param $node_id
     * @return false|\PDOStatement|string|\think\Collection
     * @throws DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getArticleTypeByNodeId($node_id)
    {
        $where['type.node_id'] = $node_id;
        return $this->getArticleTypeByWhere($where);
    }

    /**
     * @param $ids
     * @return false|\PDOStatement|string|\think\Collection
     * @throws DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getArticleTypeByIdArray($ids)
    {
        $where['type.id'] = ['in',$ids];
        return $this->getArticleTypeByWhere($where);
    }

    /**
     * @param $where
     * @return false|\PDOStatement|string|\think\Collection
     * @throws DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function getArticleTypeByWhere($where){
        $data =$this->alias('type')->field('type.id,name,detail,tag_id,tag')->join('type_tag','type_tag.id = tag_id','LEFT')->where($where)->select();
        return $data;
    }
}