<?php

namespace app\admin\controller;

use app\common\controller\Common;
use think\Request;
use app\common\model\EventMarketingHoliday as mark;

class Eventmarketingholiday extends Common
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $year = $this->request->get('year');
        if(empty($year)){
            $year=date("Y");
        }
        $data=(new mark())->field(["id,name,startday"])->where(["startday"=>["like","%".$year."%"]])->order("id","desc")->select();
        $arrData=collection($data)->toArray();

        $temp=array_column($arrData,"startday");
        $tempArr=[];
        for($i=0;$i<count($temp);$i++){
            if(strtotime($temp[$i])>time()){
                $tempArr[]=strtotime($temp[$i]);
            }
        }
        sort($tempArr);
        if(empty($tempArr)){
            return $this->resultArray('没有节日','',$data);
        }
        foreach($data as $item){
            if(strtotime($item->startday)==$tempArr[0]){
                $item->color=1;
            }else{
                $item->color=0;
            }
        }
        return $this->resultArray('','',$data);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}
