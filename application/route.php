<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;
Route::resource('user','common/User');
Route::resource('industry','common/Industry');
Route::resource('company','common/Company');
Route::resource('node','common/Node');
Route::resource('keyword','admin/keyword');
Route::rule('company/getAll','common/Company/getAll');
Route::rule('industry/getIndustry','common/industry/getIndustry');
Route::rule('user/getAll','common/User/getAll');
Route::rule('Industry/getIndustry','common/Industry/getIndustry');
Route::resource('node','common/Node');
Route::rule('node/status','common/Node/status');
return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    '[hello]' => [
        ':id' => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'post']],
    ],
];
