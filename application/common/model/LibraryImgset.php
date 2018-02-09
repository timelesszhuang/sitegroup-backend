<?php
/**
 * Created by IntelliJ IDEA.
 * User: qiangbi
 * Date: 1/17/18
 * Time: 11:19 AM
 */

namespace app\common\model;

use think\Model;
use app\common\controller\Common;

class LibraryImgset extends Model
{
    /***
     * 批量添加图片信息到图片集
     * 可多张传
     * @param array $srclist 图片地址数组
     * @param array $tag_ids
     * @param string $alt
     * @param string $comefrom
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function batche_add($srclist,$tag_ids=[],$alt='',$comefrom='selfadd'){
        $model = $this;
        $srclist=array_unique($srclist);
        $save_arr = [];
        $update_arr = [];
        $user = (new Common())->getSessionUserInfo();
        $node_id = $user["node_id"];
        $old_arr = $model->field('id,imgsrc,tags')->where(['imgsrc'=>['in',$srclist],'node_id'=>$node_id])->select();
        $old_src_arr=[];
        if($old_arr){
            foreach ($old_arr as $key=>$old){
                $old_tags = array_filter(explode(',',$old_arr[$key]['tags']));
                $new_tags = array_unique(array_merge($old_tags,$tag_ids));
                $old_arr[$key]['tags']=",".implode(',',$new_tags).",";
                $old_src_arr[]=$old['imgsrc'];
                $update_arr[]=['id'=>$old_arr[$key]['id'],'tags'=>$old_arr[$key]['tags']];
            }
        }
        foreach($srclist as $src){
            if(!in_array($src,$old_src_arr)){
                $save_arr[]=['imgsrc'=>$src,'tags'=>",".implode(',',array_unique($tag_ids)).",",'alt'=>$alt,'comefrom'=>$comefrom,'node_id'=>$node_id];
            }
        }
        $model->startTrans();
        try{
            if(count($update_arr)>0){
                if(!$model->saveAll($update_arr)){
                    exception('更新失败');
                }
            }
            if(count($save_arr)>0){
                if(!$model->saveAll($save_arr)){
                    exception('添加失败');
                }
            }
            $model->commit();
        }catch (\Exception $e){
            $model->rollback();
            return false;
        }
        return true;
    }

    /**
     * 获取文章中的图片列表
     * @param $content
     * @return array
     */
    public function getList($content){
        $src_list=[];
        preg_match_all('/<img[^>]+src\s*=\\s*[\'\"]([^\'\"]+)[\'\"][^>]*>/i', $content, $match);
        if(!empty($match[0])){
            foreach ($match[1] as $src){
                if (preg_match('/\s*http[s]?:\/\//', $src) === false) {
                    continue;
                }
                $src_list[] = $src;
            }
        }
        return $src_list;
    }

    /**
     * 获取单篇文章
     * @param $id
     * @return null|static
     * @throws \think\exception\DbException
     */
    public function getOne($id)
    {
        $key=self::get($id);
        return $key;
    }
}