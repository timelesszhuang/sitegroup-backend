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
Route::post('keyword/insertA','admin/keyword/insertA');
Route::resource('articletype','admin/Articletype');
Route::resource('article','admin/Article');
Route::resource('menu','admin/Menu');
Route::rule('articletype/gettype','admin/Articletype/getType');
Route::rule('company/getAll','sysadmin/Company/getAll');
Route::rule('industry/getIndustry','sysadmin/industry/getIndustry');
Route::rule('user/getAll','common/User/getAll');
Route::resource('node','sysadmin/Node');
Route::rule('node/status','sysadmin/Node/status');
Route::post('keyword/uploadKeyword','admin/keyword/uploadKeyword');
Route::post('keyword/insertKeyword','admin/keyword/insertKeyword');
Route::resource('question','admin/Question');
Route::resource('scatteredArticle','admin/Scatteredarticle');
Route::resource('scatteredTitle','admin/Scatteredtitle');
Route::resource('questionType','admin/Questiontype');
return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    '[hello]' => [
        ':id' => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'post']],
    ],
];
