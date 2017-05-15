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

Route::resource('user', 'common/User');
Route::resource('industry', 'sysadmin/Industry');
Route::resource('company', 'sysadmin/Company');

Route::resource('article', 'admin/Article');
//栏目相关
Route::resource('menu', 'admin/Menu');

//文章分类
Route::resource('articletype', 'admin/Articletype');
Route::rule('articletype/gettype', 'admin/Articletype/getType');

Route::rule('company/getAll', 'sysadmin/Company/getAll');
Route::rule('industry/getIndustry', 'sysadmin/industry/getIndustry');
Route::rule('user/getAll', 'common/User/getAll');

//节点相关操作
Route::resource('node', 'sysadmin/Node');
Route::rule('node/status', 'sysadmin/Node/status');
//关键词相关操作
Route::resource('keyword', 'admin/keyword');
//添加单条A 类关键词
Route::post('keyword/insertA', 'admin/keyword/insertA');
//批量上传关键词
Route::post('keyword/uploadKeyword', 'admin/keyword/uploadKeyword');
//插入关键词 根据已经上传的关键词
Route::post('keyword/insertKeyword', 'admin/keyword/insertKeyword');

//零散的文章
Route::resource('scatteredArticle', 'admin/Scatteredarticle');
//零散的 文章段落 标题
Route::resource('scatteredTitle', 'admin/Scatteredtitle');
//根据零散的标题获取文章详情
Route::get('scatteredTitle/getArrticleJoinTitle', 'admin/Scatteredtitle/getArrticleJoinTitle');

//问答
Route::resource('question', 'admin/Question');

//问答分类
Route::resource('questionType', 'admin/Questiontype');
Route::get('questionType/list', 'admin/Questiontype/getlist');

//模板相关操作
Route::resource('template', 'admin/template');
Route::post('template/uploadTemplate','admin/template/uploadTemplate');

return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    '[hello]' => [
        ':id' => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'post']],
    ],
];
