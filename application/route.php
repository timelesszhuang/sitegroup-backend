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

//======================================== 系统管理后台
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
//爬虫数据库采集关键词列表
Route::get('sys/getKeyword', 'sysadmin/Keyword/index');
//爬虫数据库未审核某个关键字
Route::get('sys/stopStatus/:id', 'sysadmin/Keyword/stopStatus');
//爬虫数据库已审核某个关键字
Route::get('sys/startStatus/:id', 'sysadmin/Keyword/startStatus');
//爬虫数据库启用某个关键字
Route::get('sys/startScrapy/:id', 'sysadmin/Keyword/startScrapy');
//爬虫数据库停止某个关键字
Route::get('sys/stopScrapy/:id', 'sysadmin/Keyword/stopScrapy');
//微信采集文章列表
Route::get('sys/wecatArticle', 'sysadmin/WeixinArticle/index');
//微信采集文章获取某一篇文章
Route::get('sys/wecatArticleOne/:id', 'sysadmin/WeixinArticle/getOne');
//微信采集文章修改某一篇文章
Route::post('sys/changeWecatArticle', 'sysadmin/WeixinArticle/edit');
//微信采集文章删除某一篇文章
Route::post('sys/deleteWecatArticle/:id', 'sysadmin/WeixinArticle/delete');
//网易采集文章列表
Route::get('sys/wangyiArticle', 'sysadmin/WangyiArticle/index');
//网易采集文章获取一条
Route::get('sys/wangyiArticleOne/:id', 'sysadmin/WangyiArticle/getOne');
//网易采集文章修改一条
Route::post('sys/changewangyiArticle', 'sysadmin/WangyiArticle/edit');
//网易采集文章删除一条
Route::get('sys/deleteWangyiArticle/:id', 'sysadmin/WangyiArticle/delete');
//qq采集文章获取列表
Route::get('sys/qqArticle', 'sysadmin/QicqArticle/index');
//qq采集获取一条
Route::get('sys/qqArticleOne/:id', 'sysadmin/QicqArticle/getOne');
//qq编辑文章
Route::post('sys/changeQqArticle', 'sysadmin/QicqArticle/edit');
//qq删除文章
Route::get('sys/deleteQqArticle/:id', 'sysadmin/QicqArticle/delete');
//获取qq和网易所有分类
Route::get('sys/articleAllType', 'sysadmin/WangyiArticle/getTypes');
//微信获取关键词分类
Route::get('sys/weixinKeyList', 'sysadmin/WeixinArticle/getKeyList');
//爬虫数据库关键字分类
Route::get('sys/getKeywordType', 'sysadmin/KeywordType/index');
//分类添加
Route::post('sys/addKeywordType', 'sysadmin/KeywordType/addKeywordType');
//分类修改
Route::post('sys/editKeywordType', 'sysadmin/KeywordType/editKeywordType');
//获取一条
Route::get('sys/keywordtype/:id', 'sysadmin/KeywordType/read');
//获取分类列表
Route::post('sys/getKeyTypeList', 'sysadmin/KeywordType/getKeyTypeList');
//添加爬虫关键词
Route::post('sys/addKeyword', 'sysadmin/Keyword/addKeyword');
//修改爬虫关键词
Route::post('sys/editKeyword', 'sysadmin/Keyword/editKeyword');
//爬虫数据库scrapy获取单条数据
Route::get('sys/gettype/:id', 'sysadmin/Keyword/read');
//最热新闻所有数据
Route::get('sys/hotnews', 'sysadmin/Hotnews/index');
//修改
Route::post('sys/editnews', 'sysadmin/Hotnews/editnews');
//一条数据
Route::get('sys/getonenews/:id', 'sysadmin/Hotnews/getOne');
//营销创意
Route::resource('sys/Marketingmode','sysadmin/Marketingmode');
//节点列表
Route::get('sys/getNodelist','sysadmin/Node/nodeList');
//系统推送
Route::resource('sys/systemNotice','sysadmin/SystemNotice');
//营销中心
Route::resource('sys/CaseCenter',"sysadmin/CaseCenter");
//营销模式上传缩略图
Route::post('sys/uploadMarketingmode','sysadmin/Marketingmode/uploadImage');
//营销事件活动
Route::resource('sys/eventmarketholiday','sysadmin/Eventmarketingholiday');
//上传html5模板
Route::post('sys/uploadHtmlTemplate','sysadmin/HtmlTemplate/uploadTemplate');
//上传html5模板缩略图
Route::post('sys/uploadHtmlTemplateImg','sysadmin/HtmlTemplate/uploadTemplateImg');
//html5模板模块
Route::resource('sys/HtmlTemplate','sysadmin/HtmlTemplate');
//获取多条模板
Route::get('sys/AllHtmlTemplate/:id','sysadmin/HtmlTemplate/readAll');
//媒体分类
Route::resource('sys/mediaType','sysadmin/MediaType');
//媒体
Route::resource('sys/meida','sysadmin/Media');













//====================================================================节点后台

//关键词
Route::resource('keyword', 'admin/keyword');
Route::post('keyword/insertA', 'admin/keyword/insertA');
Route::post('keyword/uploadKeyword', 'admin/keyword/uploadKeyword');
Route::post('keyword/insertKeyword', 'admin/keyword/insertKeyword');
Route::get('keyword/KeywordCount', 'admin/keyword/KeywordCount');

//文章分类
Route::resource('articletype', 'admin/Articletype');
Route::rule('articletype/gettype', 'admin/Articletype/getType');

//文章
Route::resource('article', 'admin/Article');
Route::post('article/sync', 'admin/Article/syncArticle');
Route::get('article/getErrorInfo', 'admin/Article/getErrorInfo');
Route::get('article/getErrorStatus', 'admin/Article/getErrorStatus');
Route::post('article/changeErrorStatus/:id', 'admin/Article/changeErrorStatus');
Route::get('articletype/articleCount', 'admin/Articletype/ArticleCount');


//菜单
Route::resource('menu', 'admin/Menu');
Route::get('menu/getMenu', 'admin/Menu/getMenu');

//问答
Route::resource('question', 'admin/Question');


//问答分类
Route::resource('questionType', 'admin/Questiontype');
Route::get('questionType/list', 'admin/Questiontype/getQuestionType');
//统计问答
Route::get('questiontype/QuestionCount', 'admin/Questiontype/QuestionCount');
//段落文章
Route::resource('scatteredArticle', 'admin/Scatteredarticle');

//段落标题
Route::resource('scatteredTitle', 'admin/Scatteredtitle');
Route::get('scatteredTitle/getArrticleJoinTitle', 'admin/Scatteredtitle/getArrticleJoinTitle');

//公共代码
Route::resource('code', 'admin/Code');
Route::get('code/getAll', 'admin/Code/getCodes');

//域名管理
Route::resource('domain', 'admin/domain');
Route::get('domain/getDomain', 'admin/domain/getDomain');
Route::get('domain/getOffice', 'admin/domain/getOffice');

//模板相关操作
Route::resource('template', 'admin/template');
Route::post('template/uploadTemplate', 'admin/template/uploadTemplate');
Route::post('template/addTemplate', 'admin/template/addTemplate');
Route::get('template/getTemplate', 'admin/Template/getTemplate');

//活动相关操作
Route::resource('activity', 'admin/activity');
//上传活动信息
Route::rule('activity/uploadActivity', 'admin/activity/uploadActivity');
Route::post('activity/addActivity', 'admin/activity/addActivity');
Route::put('activity/changeActivityStatus', 'admin/activity/changeActivityStatus');

//联系方式
Route::resource('contactway', 'admin/Contactway');
Route::get('contactway/getContactway', 'admin/Contactway/getContactway');

//站点用户
Route::resource('siteuser', 'admin/Siteuser');
Route::put('siteuser/enable', 'admin/Siteuser/enable');
Route::get('siteuser/getUsers', 'admin/Siteuser/getUsers');

//站点分类
Route::resource('sitetype', 'admin/Sitetype');
Route::get('sitetype/getSiteType', 'admin/Sitetype/getSiteType');
//站点统计
Route::get('site/SiteCount', 'admin/Site/SiteCount');

//站点管理
Route::resource('Site', 'admin/Site');
Route::get('Site/uploadTemplateFile', 'admin/Site/uploadTemplateFile');
Route::post('Site/setMainSite', 'admin/Site/setMainSite');
Route::put('Site/saveFtp/:id', 'admin/Site/saveFtp');
Route::get('Site/mobileSite', 'admin/Site/mobileSite');
Route::get('Site/flow', 'admin/Site/flow');
//站点静态化配置
Route::resource('Staticconfig', 'admin/Staticconfig');
//统计搜索
Route::get('enginecount', 'admin/Site/enginecount');

//发送模板
Route::get('Site/ignoreFrontend/:template_id/:site_id/:type', 'admin/Site/ignoreFrontend');

Route::get('Site/getSites', 'admin/Site/getSites');
//一键生成 生成文章 清除缓存 生成栏目 生成首页
Route::get('Site/siteGetCurl/:id/:name', 'admin/Site/siteGetCurl');
//获取活动模板信息
Route::get('Site/getActivily/:id', 'admin/Site/getActivily');
Route::get('commontype', 'admin/Site/commontype');

//测试文件接收  实际应该写在小节点中
Route::rule('testsendFile/index', 'admin/testsendFile/index');

//友情链接
Route::resource('links', 'admin/Links');
Route::get('links/getLinks', 'admin/Links/getLinks');
//节点统计
Route::resource("count", 'admin/Count');
Route::resource("countkeyword", 'admin/CountKeyword');
Route::get("count/enginecount", 'admin/Count/enginecount');
Route::get("count/en", 'admin/Count/en');
Route::get("count/pv", 'admin/Count/pv');
Route::get("count/show", 'admin/Count/show');
Route::get("count/articlecount", 'admin/Count/ArticleCount');
//浏览量
Route::resource('pv', 'admin/Pv');

//甩单
Route::resource('Rejection', 'admin/Rejection');
//crontab任务,每天定时执行更新所有网站的静态页面
Route::get('crontab', 'admin/CrontabTask/index');
//模板管理 获取对应site_id的信息
Route::get("templateList/:site_id", "admin/template/filelist");
// 模板管理 读取模板
Route::get("templateRead/:site_id/:name", "admin/template/templateRead");
// 模板管理 修改模板
Route::post("templateSave/:site_id/:name", "admin/template/save");
//模板管理 新加模板
Route::post("templateAdd/:site_id/:name", "admin/template/readFile");
//大站点可以统一修改小站点的tdk
Route::put("tdk/:id", 'admin/Tdk/save');
//大站点统一查询小站点
Route::get("getTdk/:id", 'admin/Tdk/search');
//大站点获取小站点的一条记录
Route::get("getTdkOne/:id", 'admin/Tdk/read');
//爬虫数据库scrapy的关键字查询
Route::get('scrapy/getKeyword', 'admin/WeixinKeyword/index');
//爬虫数据库scrapy添加关键字
Route::post('scrapy/addKeyword', 'admin/WeixinKeyword/create');
//爬虫数据库scrapy修改关键字
Route::post('scrapy/saveKeyword', 'admin/WeixinKeyword/save');
//爬虫数据库scrapy根据id获取关键字
Route::get('scrapy/getOneKeyword/:id', 'admin/WeixinKeyword/read');
//爬虫数据库scrapy获取列表
Route::get('scrapy/getlist', 'admin/WeixinKeyword/getKeyList');
//wechat采集文章列表
Route::get('wechat/article', 'admin/WeixinArticle/index');
//wechat采集文章添加到文章库
Route::post('wechat/addArticle', 'admin/WeixinArticle/create');
//wechat获取一篇采集文章
Route::get('wechat/getOneArticle/:id', 'admin/WeixinArticle/read');
//网易采集文章列表
Route::get('wangyi/article', 'admin/WangyiArticle/index');
//网易采集文章添加到文章库
Route::post('wangyi/addArticle', 'admin/WangyiArticle/create');
//网易采集文章获取一篇采集文章
Route::get('wangyi/getOneArticle/:id', 'admin/WangyiArticle/read');
//qq采集文章列表
Route::get('qq/article', 'admin/QicqArticle/index');
//qq采集文章添加到文章库
Route::post('qq/addArticle', 'admin/QicqArticle/create');
//qq采集文章获取一篇采集文章
Route::get('qq/getOneArticle/:id', 'admin/QicqArticle/read');
//网易和qq获取分类
Route::get('article/articleAllType', 'admin/WangyiArticle/getTypes');

//推荐关键词
Route::get('admin/mainkeywords', 'admin/MainkeywordKeywords/index');
Route::get('admin/searchkeywords', 'admin/MainkeywordSearch/index');

//A类关键词分类
Route::get('admin/mainkeyword', 'admin/MainkeywordSearch/mainkeyword');
Route::get('admin/getKeyTypeList', 'admin/KeywordType/getKeyTypeList');
//零散段落的分类下拉获取
Route::get('sca/getType', 'admin/Scatteredarticletype/getType');
//零散段落表格数据
Route::resource('sca/all', 'admin/Scatteredarticletype');
//hotnews获取
Route::resource('admin/hotnews', 'admin/Hotnews');
//栏目分类
Route::resource('admin/menutag', 'admin/Menutag');
//获取分类列表
Route::get('admin/menutag/list', 'admin/Menutag/getTags');
//textarea的格式插入关键词
Route::post('admin/insertTag', 'admin/Keyword/insertByTag');
//删除所有关键词 只要没有下级
Route::post('admin/deleAll', 'admin/Keyword/deleteAll');
//获取tdk中的a类关键词
Route::get('admin/getAkeywordA/:id', 'admin/Tdk/getAkeyword');
//修改a类关键词pageinfo
Route::post('admin/editpageinfo', 'admin/Tdk/editpageinfo');

//自定义表单设置
Route::resource('admin/userdefinedform', 'admin/UserDefinedForm');
//获取自定义表单代码
Route::get('admin/userdefinedformcode/:id', 'admin/UserDefinedForm/getFormCode');
//获取所有类型
Route::get('admin/userdefine', 'admin/Rejection/getUserDefind');
//产品分类
Route::resource('admin/productType','admin/ProductType');
//获取产品分类列表
Route::get('admin/getProductType','admin/ProductType/getTypes');
//产品
Route::resource('admin/product','admin/Product');
//产品图片
Route::post('admin/uploadProductImg','admin/Product/uploadImage');
//营销策略
Route::resource('admin/Marketingmode','admin/Marketingmode');
//获取行业分类
Route::get('admin/getIndustry','admin/Industry/getIndustry');
//系统推送
Route::resource('admin/systemNotice','admin/SystemNotice');
//案例中心
Route::resource('admin/CaseCenter','admin/CaseCenter');
//修改关键词根据id和名称
Route::get('admin/updateKeyword/:id/:name','admin/Keyword/updateKeyword');
//事件营销活动
Route::resource('admin/eventmarketholiday','admin/Eventmarketingholiday');
//获取多条模板
Route::get('admin/AllHtmlTemplate/:id','admin/HtmlTemplate/readAll');
//html5模板模块
Route::resource('admin/HtmlTemplate','admin/HtmlTemplate');
//事件营销记录
Route::resource('admin/eventRecord','admin/EventMarketingHolidayRecord');
//追踪关键词
Route::resource('admin/trackKeyword','admin/TrackKeyword');
//首页统计
Route::get('admin/countDatas','admin/Pv/countDatas');

Route::get('admin/gettrack','admin/TrackKeyword/getTrack');
//获取前4条 营销图片和id
Route::get('admin/getFourMarket','admin/Marketingmode/getFour');


















//站点相关--------------------------------------------------------
//站点登录后的首页操作
Route::post('user/siteInfo', 'user/index/siteInfo');

//站点文章
Route::resource('user/article', 'user/Article');
Route::get('user/articleType', 'user/Article/getArticleType');
Route::get('user/getErrorInfo', 'user/Article/getErrorInfo');
Route::post('user/changeErrorStatus/:id', 'user/Article/changeErrorStatus');
Route::get('user/getErrorStatus/', 'user/Article/getErrorStatus');

//问答
Route::resource('user/question', 'user/question');

//页面tdk修改
Route::resource('user/pageInfo', 'user/PageInfo');

//外部访问 操作 甩单、关键词等
Route::resource('user/externalAccess', 'common/ExternalAccess');
//小站点 一键生成 生成文章 清除缓存 生成栏目 生成首页
Route::get('user/siteGetCurl/:id/:name', 'user/Article/siteGetCurl');
//站点统计
Route::resource('user/acount', 'user/Acount');
Route::resource('user/keyword', 'user/Keyword');
Route::get('user/articleCount', 'user/Article/ArticleCount');
//ArticleCount
//站点浏览量pv统计
Route::resource('user/Statistics', 'user/Statistics');
Route::get('user/pv', 'user/Statistics/pv');
Route::get('user/enginecount', 'user/Statistics/enginecount');
//小站点插入href
Route::resource('user/ArticleInsertA', 'user/ArticleInsertA');
//小站点替换关键词
Route::resource('user/Substitution', 'user/Substitution');
//小站点静态化配置
Route::resource('user/Staticconfig', 'user/Staticconfig');
//小站点 查询模板文件
Route::post('user/template/:site_id', 'user/Template/index');
//小站点 获取模板内容
Route::post('user/readtemplate/:site_id/:name', 'user/Template/read');
//小站点 修改模板
Route::post('user/savetemplate/:site_id/:name', 'user/Template/save');
//小站点 添加模板
Route::post('user/addtemplate/:site_id/:name', 'user/Template/story');
//爬虫数据库scrapy的关键字查询
Route::get('user/scrapy/getKeyword', "user/WeixinKeyword/index");
//爬虫数据库scrapy添加关键字
Route::get('user/scrapy/Keyword', "user/WeixinKeyword/create");
//爬虫数据库scrapy修改关键字
Route::get('user/scrapy/saveKeyword/', 'user/WeixinKeyword/save');
//wechat文章列表
Route::get('user/wechat/article', 'user/WechatArticle/index');
//wechat获取一条数据
Route::get('user/wechat/getArticleOne/:id', 'user/WechatArticle/read');
//wechat添加到文章库
Route::post('user/wechat/addArticle', 'user/WechatArticle/create');
//关键词列表
Route::get('user/keywordGetlist', 'user/WeixinKeyword/getKeyList');
//网易采集文章列表
Route::get('user/wangyiArticle', 'user/WangyiArticle/index');
//网易采集文章添加到文章库
Route::post('user/wangyiAddArticle', 'user/WangyiArticle/create');
//网易采集文章获取一篇采集文章
Route::get('user/WangyiOneArticle/:id', 'user/WangyiArticle/getOne');
//qq采集文章列表
Route::get('user/qqArticle', 'user/QicqArticle/index');
//qq采集文章添加到文章库
Route::post('user/QQaddArticle', 'user/QicqArticle/create');
//qq采集文章获取一篇采集文章
Route::get('user/QQOneArticle/:id', 'user/QicqArticle/getOne');
//网易和qq获取分类
Route::get('user/articleAllType', 'user/WangyiArticle/getTypes');
//热点新闻
Route::resource('user/hotnews', 'user/Hotnews');
//获取tdk中的a类关键词
Route::get('user/getAkeyword', 'user/PageInfo/getAkeyword');
//文章tdk修改
Route::get('user/articletdk', 'user/PageInfo/articletdk');
Route::get('user/articletdksave', 'user/PageInfo/articletdksave');
Route::post('user/articletdkedit', 'user/PageInfo/articletdkedit');
//问答tdk修改
Route::get('user/questiontdk', 'user/PageInfo/questiontdk');
Route::get('user/questiontdksave', 'user/PageInfo/questiontdksave');
Route::post('user/questiontdkedit', 'user/PageInfo/questiontdkedit');
//产品tdk修改
Route::get('user/producttdk', 'user/PageInfo/producttdk');
Route::get('user/producttdksave', 'user/PageInfo/producttdksave');
Route::post('user/producttdkedit', 'user/PageInfo/producttdkedit');
//修改a类关键词pageinfo
Route::post('user/editpageinfo', 'user/PageInfo/editpageinfo');
//关键词替换指定的链接
Route::resource('user/articleReplaceKeyword','user/ArticleReplaceKeyword');
//统计所有
Route::get('user/getFour','tool/Pv/countDatas');

//return [
//    '__pattern__' => [
//        'name' => '\w+',
//    ],
//    '[hello]' => [
//        ':id' => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
//        ':name' => ['index/hello', ['method' => 'post']],
//    ],
//];
