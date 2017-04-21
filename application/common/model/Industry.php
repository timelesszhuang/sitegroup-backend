<?php
/**
 * Created by PhpStorm.
 * User: 赵甲戌
 * Date: 2017/4/21
 * Time: 11:35
 */
namespace app\common\model;
class Industry extends Model{
    protected $rule = [
        ['name', 'require', '公司名必须'],
        ['detail', 'require', '详细必须'],
    ];
}