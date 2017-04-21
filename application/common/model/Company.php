<?php
/**
 * Created by PhpStorm.
 * User: 赵甲戌
 * Date: 2017/4/21
 * Time: 11:34
 */
namespace app\common\model;
class Company extends Model{
    protected $rule = [
        ['name', 'require', '公司名必须'],
        ['url','require','网址必须'],
        ['url','require','网址必须'],
    ];
}