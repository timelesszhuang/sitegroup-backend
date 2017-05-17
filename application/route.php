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

//用户
Route::resource('user', 'common/User');
Route::rule('user/getAll', 'common/User/getAll');

//行业
Route::resource('industry', 'sysadmin/Industry');
Route::rule('industry/getIndustry', 'sysadmin/industry/getIndustry');

//公司
Route::resource('company', 'sysadmin/Company');
Route::rule('company/getAll', 'sysadmin/Company/getAll');

//节点
Route::resource('node', 'sysadmin/Node');
Route::rule('node/status', 'sysadmin/Node/status');

//关键词
Route::resource('keyword', 'admin/keyword');
Route::post('keyword/insertA', 'admin/keyword/insertA');
Route::post('keyword/uploadKeyword', 'admin/keyword/uploadKeyword');
Route::post('keyword/insertKeyword', 'admin/keyword/insertKeyword');

//文章分类
Route::resource('articletype', 'admin/Articletype');
Route::rule('articletype/gettype', 'admin/Articletype/getType');

//文章
Route::resource('article', 'admin/Article');
Route::post('article/sync','admin/Article/syncArticle');

//菜单
Route::resource('menu', 'admin/Menu');

//问答
Route::resource('question', 'admin/Question');

//问答分类
Route::resource('questionType', 'admin/Questiontype');
Route::get('questionType/list', 'admin/Questiontype/getlist');

//段落文章
Route::resource('scatteredArticle', 'admin/Scatteredarticle');

//段落标题
Route::resource('scatteredTitle', 'admin/Scatteredtitle');
Route::get('scatteredTitle/getArrticleJoinTitle', 'admin/Scatteredtitle/getArrticleJoinTitle');

//公共代码
Route::resource('code', 'admin/Code');

//域名管理
Route::resource('domain', 'admin/domain');

//模板相关操作
Route::resource('template', 'admin/template');
Route::post('template/uploadTemplate', 'admin/template/uploadTemplate');
Route::post('template/addTemplate', 'admin/template/addTemplate');

//活动相关操作
Route::resource('activity', 'admin/activity');
//上传活动信息
Route::rule('activity/uploadActivity', 'admin/activity/uploadActivity');
Route::post('activity/addActivity', 'admin/activity/addActivity');
Route::put('activity/changeActivityStatus', 'admin/activity/changeActivityStatus');

//联系方式
Route::resource('contactway', 'admin/Contactway');

//站点用户
Route::resource('siteuser', 'admin/Siteuser');
Route::put('siteuser/enable', 'admin/Siteuser/enable');

//站点分类
Route::resource('sitetype', 'admin/Sitetype');

//站点管理
Route::resource('Site', 'admin/Site');
Route::get('Site/uploadTemplateFile', 'admin/Site/uploadTemplateFile');

//测试文件接收  实际应该写在小节点中
Route::rule('testsendFile/index', 'admin/testsendFile/index');


return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    '[hello]' => [
        ':id' => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'post']],
    ],
];
