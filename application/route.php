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
Route::get('article/getErrorInfo','admin/Article/getErrorInfo');
Route::get('article/getErrorStatus','admin/Article/getErrorStatus');
Route::post('article/changeErrorStatus/:id','admin/Article/changeErrorStatus');

//菜单
Route::resource('menu', 'admin/Menu');
Route::get('menu/getMenu','admin/Menu/getMenu');

//问答
Route::resource('question', 'admin/Question');

//问答分类
Route::resource('questionType', 'admin/Questiontype');
Route::get('questionType/list', 'admin/Questiontype/getQuestionType');

//段落文章
Route::resource('scatteredArticle', 'admin/Scatteredarticle');

//段落标题
Route::resource('scatteredTitle', 'admin/Scatteredtitle');
Route::get('scatteredTitle/getArrticleJoinTitle', 'admin/Scatteredtitle/getArrticleJoinTitle');

//公共代码
Route::resource('code', 'admin/Code');
Route::get('code/getAll','admin/Code/getCodes');

//域名管理
Route::resource('domain', 'admin/domain');
Route::get('domain/getDomain','admin/domain/getDomain');
Route::get('domain/getOffice','admin/domain/getOffice');

//模板相关操作
Route::resource('template', 'admin/template');
Route::post('template/uploadTemplate', 'admin/template/uploadTemplate');
Route::post('template/addTemplate', 'admin/template/addTemplate');
Route::get('template/getTemplate','admin/Template/getTemplate');

//活动相关操作
Route::resource('activity', 'admin/activity');
//上传活动信息
Route::rule('activity/uploadActivity', 'admin/activity/uploadActivity');
Route::post('activity/addActivity', 'admin/activity/addActivity');
Route::put('activity/changeActivityStatus', 'admin/activity/changeActivityStatus');

//联系方式
Route::resource('contactway', 'admin/Contactway');
Route::get('contactway/getContactway','admin/Contactway/getContactway');

//站点用户
Route::resource('siteuser', 'admin/Siteuser');
Route::put('siteuser/enable', 'admin/Siteuser/enable');
Route::get('siteuser/getUsers','admin/Siteuser/getUsers');

//站点分类
Route::resource('sitetype', 'admin/Sitetype');
Route::get('sitetype/getSiteType','admin/Sitetype/getSiteType');

//站点管理
Route::resource('Site', 'admin/Site');
Route::get('Site/uploadTemplateFile', 'admin/Site/uploadTemplateFile');
Route::post('Site/setMainSite','admin/Site/setMainSite');
Route::put('Site/saveFtp/:id','admin/Site/saveFtp');
Route::get('Site/mobileSite','admin/Site/mobileSite');
Route::get('Site/flow','admin/Site/flow');
Route::get('Site/ignoreFrontend/:id','admin/Site/ignoreFrontend');
Route::get('Site/syncTemplate/:id/:nid','admin/Site/syncTemplate');
Route::get('Site/getSites','admin/Site/getSites');

//测试文件接收  实际应该写在小节点中
Route::rule('testsendFile/index', 'admin/testsendFile/index');

//友情链接
Route::resource('links','admin/Links');
Route::get('links/getLinks','admin/Links/getLinks');
//节点统计
Route::resource("count",'admin/Count');
Route::resource("countkeyword",'admin/CountKeyword');





//小站点相关--------------------------------------------------------
//小站点登录后的首页操作
Route::post('user/siteInfo','user/index/siteInfo');

//小站点文章
Route::resource('user/article','user/Article');
Route::get('user/articleType','user/Article/getArticleType');
Route::get('user/getErrorInfo','user/Article/getErrorInfo');
Route::post('user/changeErrorStatus/:id','user/Article/changeErrorStatus');
Route::get('user/getErrorStatus/','user/Article/getErrorStatus');
//小站点统计
Route::resource('user/acount','user/Acount');
Route::resource('user/keyword','user/Keyword');
//问答
Route::resource('user/question','user/question');

//页面tdk修改
Route::resource('user/pageInfo','user/PageInfo');

//外部访问 操作 甩单、关键词等
Route::resource('user/externalAccess','common/ExternalAccess');





return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    '[hello]' => [
        ':id' => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'post']],
    ],
];
