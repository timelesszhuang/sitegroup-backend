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
Route::resource('industry','sysadmin/Industry');
Route::resource('company','sysadmin/Company');
Route::resource('node','sysadmin/Node');
Route::resource('keyword','admin/keyword');
Route::rule('company/getAll','sysadmin/Company/getAll');
Route::rule('industry/getIndustry','sysadmin/industry/getIndustry');
Route::rule('user/getAll','common/User/getAll');
Route::resource('node','sysadmin/Node');
Route::rule('node/status','sysadmin/Node/status');
Route::post('keyword/uploadKeyword','admin/keyword/uploadKeyword');
Route::post('keyword/insertKeyword','admin/keyword/insertKeyword');
return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    '[hello]' => [
        ':id' => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'post']],
    ],
];
