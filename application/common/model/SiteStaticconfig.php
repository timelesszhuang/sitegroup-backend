<?php
/**
 * Created by PhpStorm.
 * User: qiangbi
 * Date: 17-4-26
 * Time: 下午2:25
 */

namespace app\admin\model;
use think\Model;

class SiteStaticconfig extends Model
{
    //只读字段
    protected $readonly=["node_id"];
    /**
     * 获取所有 文章
     * @param $limit
     * @param $rows
     * @param int $where
     * @return array
     */
    public function getAll($limit,$rows,$where=0)
    {
        $count = $this->where($where)->count();
        $data = $this->limit($limit, $rows)->where($where)->select();
        foreach ($data as $v){
            $v['time']=$v['starttime'].'-'.$v['stoptime'];
        }
        return [
            "total" => $count,
            "rows" => $data
        ];
    }


}