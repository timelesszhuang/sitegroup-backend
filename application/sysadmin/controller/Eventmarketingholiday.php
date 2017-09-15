<?php

namespace app\sysadmin\controller;

use app\common\controller\Common;
use think\Request;
use app\common\model\EventMarketingHoliday as mark;
use think\Validate;

class Eventmarketingholiday extends Common
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $year=date("Y");
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
        foreach($data as $item){
            if(strtotime($item->startday)==$tempArr[0]){
                $item->color=1;
            }else{
                $item->coloe=0;
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
        $rule = [
            ["name", "require", "请输入节日|节日重复"],
            ['time', 'require', '请输入日期'],
        ];

        $data = $this->request->post();
        $validate = new Validate($rule);
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), 'failed');
        }
        $data["startday"]=date("Y-m-d",strtotime($data["time"][0]));
        $data["endday"]=date("Y-m-d",strtotime($data["time"][1]));
        unset($data["time"]);
        if (!mark::create($data)) {
            return $this->resultArray('添加失败', 'failed');
        }
        return $this->resultArray('添加成功');
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        $data=mark::get($id);
        $day=$data->endday;
        if(empty($day)){
            $day=$data->startday;
        }
        $data->time=[$data->startday,$day];
        return $this->resultArray('','',$data);
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
        $rule = [
            ["name", "require|unique:Industry", "请输入节日|节日重复"],
            ['time', 'require', '请输入起始日期'],
        ];
        $data = $this->request->post();
        $validate = new Validate($rule);
        if (!$validate->check($data)) {
            return $this->resultArray($validate->getError(), 'failed');
        }
        $data["startday"]=date("Y-m-d",strtotime($data["time"][0]));
        $data["endday"]=date("Y-m-d",strtotime($data["time"][1]));
        unset($data["time"]);
        if (!mark::update($data)) {
            return $this->resultArray('修改失败', 'failed');
        }
        return $this->resultArray('修改成功');
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $mark = mark::get($id);
        if (!$mark->delete()) {
            return $this->resultArray('删除失败', 'failed');
        }
        return $this->resultArray('删除成功');
    }

    public function juhecurl($url,$params=false,$ispost=0){
        $httpInfo = array();
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1 );
        curl_setopt( $ch, CURLOPT_USERAGENT , 'JuheData' );
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT , 60 );
        curl_setopt( $ch, CURLOPT_TIMEOUT , 60);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER , true );
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        if( $ispost )
        {
            curl_setopt( $ch , CURLOPT_POST , true );
            curl_setopt( $ch , CURLOPT_POSTFIELDS , $params );
            curl_setopt( $ch , CURLOPT_URL , $url );
        }
        else
        {
            if($params){
                curl_setopt( $ch , CURLOPT_URL , $url.'?'.$params );
            }else{
                curl_setopt( $ch , CURLOPT_URL , $url);
            }
        }
        $response = curl_exec( $ch );
        if ($response === FALSE) {
            return false;
        }
        $httpCode = curl_getinfo( $ch , CURLINFO_HTTP_CODE );
        $httpInfo = array_merge( $httpInfo , curl_getinfo( $ch ) );
        curl_close( $ch );
        $data = json_decode($response);
        return (json_decode($data->result->data->holidaylist));
    }

    public function ceshi($year=2017){
        $url = "http://v.juhe.cn/calendar/year";
        $params = array(
            "key" => "24d7c66a5b09ca8015223616c59d107e",//您申请的appKey
            "year" => $year,//指定年份,格式为YYYY,如:2015
        );
        $paramstring = http_build_query($params);
        $content = $this->juhecurl($url,$paramstring);
        $result = json_decode($content,true);
        if($result){
            if($result['error_code']=='0'){
                print_r($result);
            }else{
                echo $result['error_code'].":".$result['reason'];
            }
        }else{
            echo "请求失败";
        }
    }



}
