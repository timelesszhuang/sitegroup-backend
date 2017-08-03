<?php

namespace app\admin\model;

use think\Db;
use think\Model;

class  MainkeywordKeywords extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'sc_mainkeyword_keywords';

    protected $connection = [
        // 数据库类型
        'type'        => 'mysql',
        // 数据库连接DSN配置
        'dsn'         => '',
        // 服务器地址
        'hostname'    => 'rdsfjnifbfjnifbo.mysql.rds.aliyuncs.com',
//        // 数据库名
        'database'    => 'scrapy',
        // 数据库用户名
        'username'    => 'scrapy',
        // 数据库密码
        'password'    => '201671Zhuang',
        // 数据库连接端口
        'hostport'    => '',
        // 数据库连接参数
        'params'      => [],
        // 数据库编码默认采用utf8
        'charset'     => 'utf8',
        // 数据库表前缀
        'prefix'      => 'sc_',
    ];


    /**
     * 格式化日期
     * @param $value
     * @param $key
     */
    public function formatter_date(&$value,$key)
    {
        if($value['create_time']){
            $value['create_time']=date("Y-m-d H:i:s",$value['create_time']);
        }
    }
    /**
     * 获取所有关键字分类
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getType($limit, $rows, $where = 0)
    {
        $count = $this->where($where)->count();
        $data=Db::connect($this->connection)->table("sc_mainkeyword_keywords")->where($where)->limit($limit, $rows)->order('count desc')->select();
        array_walk($data,[$this,'formatter_date']);
        return [
            "total" => $count,
            "rows" => $data
        ];
    }

    /**
     * 添加关键字
     * @param $name
     * @return $this
     */
    public function addKeyword($name)
    {
        return self::create(["name"=>$name,"status"=>10]);
    }

    /**
     * 修改关键词
     * @param $id
     * @param $name
     * @return false|int
     */
    public function editKeyword($id,$name)
    {
        return Db::connect($this->connection)->table("sc_weixin_keyword")->order('id desc')->where(["id"=>$id])->update(["name"=>$name]);
    }

    /**
     * 获取一条数据
     * @param $id
     * @return null|static
     */
    public function getOne($id)
    {
        $key=self::get($id);
        return $key;
    }

 public function getKeyTypeList()
    {
        return Db::connect($this->connection)->table("sc_weixin_keyword_type")->field("id,name as text")->select();
    }

}
