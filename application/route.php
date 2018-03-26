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
use think\Request;
use think\Route;

/** 登录相关*/
Route::get('get_session', 'common/Login/getSession');//获取当前用户数据
Route::get('clear_session', 'common/Login/clearSession');//清楚当前用户登录session信息
//测试接口
Route::get('test',"admin/Oschina/test");
Route::get('test1',"common/Common/test");
//登录
Route::post('login', 'common/Login/login');//用户登录
//自动登录
Route::post('auto_login', 'common/Login/autoLogin');//自动登录
//获取验证码
//Route::get('captcha');//获取一个新的验证码
//更新密码
Route::post('change_password', 'common/AccountOperation/changePassword');//用户修改密码
//退出登录
Route::get('logout','common/Login/logout');//退出登录
//分类标签
Route::resource('type_tag', 'common/TypeTag');
//登陆后获取站点列表
Route::get('get_site_list','common/Login/siteList');//登陆后获取站点列表
//登陆后设置站点信息
Route::post('set_site_info','common/Login/setSiteInfo');//登陆后设置站点信息
/** 获取主页信息*/
Route::get('home_info','common/Home/getLanderInfo');//登陆后获取首页信息
Route::get('home_count','common/Home/countDatas');
Route::get('root_count','common/Home/RootCountDatas');
Route::get('site_count','common/Home/siteCountDatas');
Route::get('home_marketmode', 'common/Home/getMarketMode');//获取前6条 营销模式图片和id
Route::get('home_casecenter', 'common/Home/getCaseCenter');//获取前6条 案例中心图片和id

Route::get('home_en', 'common/Home/en');//获取前4条 营销图片和id
Route::get('pv_show', 'common/Home/show');//获取前4条 营销图片和id
/** 内容管理*/
//获取分类列表
Route::get('get_type_list','common/Types/getType');
Route::resource('type','common/Types');
//文章相关
Route::resource('article','common/Article');
Route::post('article_csv_import','common/Article/csvimport');
Route::post('article_show_html','common/Article/articleShowHtml');
//问答相关
Route::resource('question','common/Question');
Route::resource('wx_question','wx/Question');
Route::post('question_show_html','common/Question/questionShowHtml');
//产品相关
Route::resource('product','admin/Product');

Route::post('product_show_html','admin/Product/productShowHtml');
Route::post('upload_product_ser_img', 'admin/Product/uploadImgSer');
Route::get('get_product_img_list/:id', 'admin/Product/getImgSer');
Route::get('delete_product_img/:id/:index', 'admin/Product/deleteImgser');
//图集
Route::resource('imglist', 'admin/ImgList');
Route::get('change_imglist_status/:id/:status', 'admin/ImgList/changeStatus');
Route::get('get_imgser/:id', 'admin/ImgList/getImgSer');
Route::post('upload_img_list_imgser', 'admin/ImgList/uploadImgSer');
Route::get('delete_imgser/:id/:index', 'admin/ImgList/deleteImgser');
Route::post('save_imglist_info', 'admin/ImgList/saveInfo');
//图片上传
Route::post('image_upload',function(){
    Request::instance()->module('common');
    Request::instance()->controller('OssUpload');
    Request::instance()->action('imageUpload');
    return (new \app\common\controller\OssUpload())->imageUpload('pic');});
Route::post('article_image_upload',function(){
    Request::instance()->module('common');
    Request::instance()->controller('OssUpload');
    Request::instance()->action('imageUpload');
    return (new \app\common\controller\OssUpload())->imageUpload('article');});
Route::post('question_image_upload',function(){
    Request::instance()->module('common');
    Request::instance()->controller('OssUpload');
    Request::instance()->action('imageUpload');
    return (new \app\common\controller\OssUpload())->imageUpload('question');});
Route::post('product_image_upload',function(){
    Request::instance()->module('common');
    Request::instance()->controller('OssUpload');
    Request::instance()->action('imageUpload');
    return (new \app\common\controller\OssUpload())->imageUpload('product/mainimg');});
Route::post('library_image_upload',function(){
    Request::instance()->module('common');
    Request::instance()->controller('OssUpload');
    Request::instance()->action('imageUpload');
    return (new \app\common\controller\OssUpload())->imageUpload('libraryimgset');});
//csv上传
Route::post('article_csv_upload',function(){
    Request::instance()->module('common');
    Request::instance()->controller('OssUpload');
    Request::instance()->action('csvUpload');
    return (new \app\common\controller\OssUpload())->csvUpload('article/csv');});
//标签获取
Route::get('get_tags', 'common/Tags/getTagList');
Route::resource('tags', 'common/Tags');
//公共图片资源路由
Route::resource('library_imgset',"common/LibraryImgset");
/** 资源聚合*/
//搜狐新闻
Route::resource('souhu',"common/Souhu");
//搜狐分类列表
Route::get('souhu_type_list',"common/Souhu/getTypes");
//腾讯新闻
Route::resource('qicq',"common/Qicq");
//腾讯分类列表
Route::get('qicq_type_list',"common/Qicq/getTypes");
//网易新闻
Route::resource('wangyi',"common/Wangyi");
//网易分类列表
Route::get('wangyi_type_list',"common/Wangyi/getTypes");
//热点新闻
Route::resource('hot_news',"common/Hotnews");
/** 素材库*/
Route::resource('public_article',"common/LibraryArticle");
Route::resource('public_image',"common/LibraryImgset");
/** 关键词*/
Route::resource('keyword',"admin/Keyword");
Route::resource('district',"admin/District");
Route::post('keyword_file',"admin/Keyword/getKeywordByFile");
Route::post('delete_keywords',"admin/Keyword/deleteAll");
Route::get('keyword_count',"admin/Keyword/keywordCount");
/** 行业*/
Route::resource('industry',"common/Industry");
Route::get('industries',"common/Industry/getIndustry");
/** 营销模式*/
Route::resource('marketing_mode',"common/Marketingmode");
/** 案例中心*/
Route::resource('case_center',"common/CaseCenter");
//系统推送
Route::resource('system_notice',"common/SystemNotice");
//获取节点下拉
Route::get('getNodelist',"common/Node/nodeList");
//递加阅读
Route::get('readcount/:id',"common/SystemNotice/readcount");
//节点数据
Route::get('nodenotice',"common/SystemNotice/nodenotice");
//阅读状态修改
Route::post('readstatus',"common/SystemNotice/readstatus");
//首页未读信息数量
Route::get('unreadnum',"common/SystemNotice/unreadnum");
//获取错误信息
Route::get('getErrorInfo',"common/SystemNotice/getErrorInfo");
//错误信息状态修改
Route::post('errorStatus',"common/SystemNotice/error_status");
//获取错误信息数量
Route::get('getErrorStatus',"common/SystemNotice/getErrorStatus");
//读取当前错误信息
Route::get('readError/:id',"common/SystemNotice/readerror");
//录音
Route::resource('voice_cdr', 'common/VoiceCdr');
//用户
Route::resource('user', 'common/User');
Route::rule('getUser', 'common/User/getAll');
//节点
Route::resource('node', 'common/Node');
//节点状态
Route::rule('node/status', 'common/Node/status');
//模板相关操作(index公用,添加为node添加修改)
Route::resource('template', 'common/template');
//总后台模板（表单）添加
Route::post('addTemp',"common/Template/addTemp");
//总后台模板（表单）修改
Route::put('oldTemplate',"common/Template/oldTemplate");
//原始模板（预览的模板）上传
Route::post('uploadOldtemplate', "common/Template/uploadOldtemplate");
//上传模板php模板
Route::post('uploadTemplate', 'common/Template/uploadTemplate');
//站点用户
Route::resource('siteuser', 'common/Siteuser');
//启用禁用状态改变
Route::put('siteuserEnable', 'common/Siteuser/enable');
//获取站点
Route::get('sitegetUsers', 'common/Siteuser/getUsers');
//公司
Route::resource('company', 'common/Company');
//获取公司下拉
Route::rule('getCompany', 'common/Company/getAll');
//栏目
Route::resource('menu', 'common/Menu');
Route::get('getMenu', 'common/Menu/getMenu');
Route::put('menuSort/:id','common/Menu/sort');
//获取上级菜单
Route::get('upMenu/:flag/:id','common/Menu/getUpMenu');
//栏目分类
Route::resource('menuTag', 'common/Menutag');
//获取分类列表
Route::get('menutagList', 'common/Menutag/getTags');

//自定义表单设置
Route::resource('userDefinedform', 'common/UserDefinedForm');
//获取自定义表单代码
Route::get('userDefinedformcode/:id', 'common/UserDefinedForm/getFormCode');

//友情链接
Route::resource('links', 'common/Links');
Route::get('getLinks', 'common/Links/getLinks');
//域名管理
Route::resource('domain', 'common/Domain');
Route::get('getDomain', 'common/Domain/getDomain');
Route::get('getOffice', 'common/Domain/getOffice');
//公共代码管理
Route::resource('code', 'common/Code');
//Route::get('getCode', 'common/Code/getCodes');

//联系方式
Route::resource('contactway', 'common/Contactway');
Route::get('getContactway', 'common/Contactway/getContactway');
//站点logo
Route::resource('siteLogo','common/SiteLogo');
//站点logo列表
Route::get('getsitelogolist',"common/SiteLogo/logoList");
//站点ico
Route::resource('siteIco','common/SiteIco');
//站点ico列表
Route::get('getsiteIcolist',"common/SiteIco/icoList");
//站点water_image
Route::resource('siteWaterImage','common/SiteWaterImage');
//站点water_image 列表
Route::get('getsitewaterimagelist',"common/SiteWaterImage/waterimageList");
//站点分类
Route::resource('siteType', 'common/Sitetype');
Route::get('getSiteType', 'common/Sitetype/getSiteType');
//获取活动模板信息
Route::get('getActivily/:id', 'common/Site/getActivily');

//站点管理
Route::resource('Site', 'common/Site');
//设为主战
Route::post('setMainSite', 'common/Site/setMainSite');
//Route::get('mobileSite', 'common/Site/mobileSite');
//发送模板
Route::get('ignoreFrontend/:template_id/:site_id/:type', 'common/Site/ignoreFrontend');
//获取站点列表
Route::get('getSites', 'common/Site/getSites');
//一键生成 生成文章 清除缓存 生成栏目 生成首页
Route::get('siteGetCurl/:id/:name', 'common/Site/siteGetCurl');
//获取活动模板信息
Route::get('getActivily/:id', 'common/Site/getActivily');
//重置站点
Route::get('resetSite/:id', 'common/Site/resetSite');
//获取各种站点下拉
Route::get('commonType', 'common/Site/commontype');
//修改Cdn 主動推送
Route::put('saveCdn/:id', 'common/Site/saveCdn');
//获取所有模板下拉
Route::get('getTemplate', 'common/Template/getTemplate');
//模板管理 获取对应site_id的信息
Route::get("templateList/:site_id/:type", "common/Template/filelist");
// 模板管理 读取模板
Route::post("templateRead", "common/Template/templateRead");
//模板管理添加文件
Route::post("templateAdd", "common/Template/readFile");
//模板静态文件上传
Route::post("uploadtemplatestatic", "common/Template/uploadtemplatestatic");
//模板修改模板
Route::post("templateRename", "common/Template/templateRename");
//模板静态文件统一修改
Route::post("updatestatic", "common/Template/updatestatic");
//大站点可以统一修改小站点的tdk
Route::put("tdk/:id", 'common/Tdk/save');
//大站点统一查询小站点
Route::get("getTdk/:id", 'common/Tdk/search');
//大站点获取小站点的一条记录
Route::get("getTdkOne/:id", 'common/Tdk/read');
//获取tdk中的a类关键词
Route::get('getAkeywordA/:id', 'common/Tdk/getAkeyword');
//修改a类关键词pageinfo
Route::post('editpageinfo', 'common/Tdk/editpageinfo');
//站点静态化配置
Route::resource('staticConfig', 'common/Staticconfig');
// 模板管理 修改模板
Route::post("templateSave", "common/template/savetemplate");
//content_get
Route::resource('content_get', 'common/ContentGet');
//推荐关键词首页数据
Route::get('mainkeywords', 'common/MainkeywordKeywords/index');
//主关键词排名
Route::get('searchKeywords', 'common/MainkeywordSearch/index');
//追踪关键词
Route::resource('trackKeyword', 'common/TrackKeyword');
//下拉追踪关键词
Route::get('getTrack', 'common/TrackKeyword/getTrack');
//甩单
Route::resource('Rejection', 'common/Rejection');
//配置下拉
Route::get('userdefine', 'common/Rejection/getUserDefind');
//浏览量展示
Route::resource('pv', 'common/Pv');
//节点统计
Route::resource("count", 'common/Count');
Route::get("countkeyword", 'common/Pv/countkeyword');
Route::get("acount", 'common/Pv/acount');
Route::get("engineCount", 'common/Count/enginecount');
//浏览量统计
Route::get("pvStatistic", 'common/Count/pvStatistic');
Route::get("articlecount", 'common/Count/ArticleCount');
Route::get("questionCount", 'common/Count/QuestionCount');
//站点搜索引擎
Route::get("searchBrowse", 'common/Count/searchBrowse');
//获取站点相关的信息
Route::get('getUserInfo', "common/UserInfo/getUserInfo");
//获取用户登陆信息
Route::get('userLoginLog', "common/UserInfo/getUserLoginList");
//活动缩略图
Route::post('uploadactivity', 'common/CreativeActivity/imageUpload');
//外站活动添加
Route::post('storyOut', 'common/CreativeActivity/storyOut');
//外部活动修改
Route::post('saveOut/:id', 'common/CreativeActivity/saveOut');
//活动相关
Route::resource('activityabout', 'common/CreativeActivity');
//活动 修改/添加 图片
Route::post('uploadactivitySerImg', 'common/CreativeActivity/uploadImgSer');
// 活动轮播获取
Route::get('getImgSer/:id', 'common/CreativeActivity/getImgSer');
//活动删除轮播
Route::get('delImgSer/:id/:index', 'common/CreativeActivity/deleteImgser');
//修改活动状态
Route::get('changeactivityStatus/:id/:status', 'common/CreativeActivity/changeStatus');
//小站点插入href
Route::resource('ArticleInsertA', 'common/ArticleInsertA');
//关键词替换指定的链接
Route::resource('articleReplaceKeyword', 'common/ArticleReplaceKeyword');
//小站点替换关键词
Route::resource('Substitution', 'common/Substitution');
////获取站点tdk中的a类关键词
Route::get('siteAkeyword', 'common/PageInfo/getAkeyword');
////栏目优化管理
Route::resource('pageInfo', 'common/PageInfo');
//文章tdk修改
Route::get('articletdk', 'common/PageInfo/articletdk');
Route::get('articletdksave', 'common/PageInfo/articletdksave');
Route::post('articletdkedit', 'common/PageInfo/articletdkedit');
//问答tdk修改
Route::get('questiontdk', 'common/PageInfo/questiontdk');
Route::get('questiontdksave', 'common/PageInfo/questiontdksave');
Route::post('questiontdkedit', 'common/PageInfo/questiontdkedit');
//产品tdk修改
Route::get('producttdk', 'common/PageInfo/producttdk');
Route::get('producttdksave', 'common/PageInfo/producttdksave');
Route::post('producttdkedit', 'common/PageInfo/producttdkedit');
//修改a类关键词pageinfo
Route::post('editpageinfo', 'common/PageInfo/editpageinfo');
//获取站点联系方式
Route::get('siteResource','common/Site/siteResource');
Route::get('readuser','common/User/readuser');
Route::post('siteResource','common/Site/editResource');
Route::any('wxlogin','common/Wxapp/login');
Route::any('wxbind','common/Wxapp/bindopenid');



/*//用户电话记录数据管理
Route::resource('voice_cdr', 'admin/VoiceCdr');
Route::resource('sys/voice_cdr', 'sysadmin/VoiceCdr');
//用户电话记录数据获取api
Route::get('omapi','common/Omapi/index');
//======================================== 系统管理后台
//公共的上传图片接口
Route::post('uploadimg', 'common/Login/imageupload');

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
Route::resource('sys/Marketingmode', 'sysadmin/Marketingmode');
//节点列表
Route::get('sys/getNodelist', 'sysadmin/Node/nodeList');
//系统推送
Route::resource('sys/systemNotice', 'sysadmin/SystemNotice');
//营销中心
Route::resource('sys/CaseCenter', "sysadmin/CaseCenter");
//营销模式上传缩略图
Route::post('sys/uploadMarketingmode', 'sysadmin/Marketingmode/uploadImage');
//html5模板模块
Route::resource('sys/HtmlTemplate', 'sysadmin/HtmlTemplate');
//媒体分类
Route::resource('sys/mediaType', 'sysadmin/MediaType');
//媒体
Route::resource('sys/media', 'sysadmin/Media');
//媒体地区
Route::get('sys/mediaOrigin', 'sysadmin/MediaType/getOrigin');
//媒体城市
Route::get('sys/getMediaType', 'sysadmin/MediaType/getTypes');
//软文
Route::resource('sys/softText', 'sysadmin/SoftText');
//软文获取地区
Route::get('sys/softGetOrigin', 'sysadmin/SoftText/getOrigin');
//软文根据地区获取媒体分类
Route::get('sys/softGetMediaType/:id', 'sysadmin/SoftText/returnsOrigin');
//设置审核状态
Route::get('sys/setCheck/:id/:num', 'sysadmin/SoftText/setCheck');
//后台上传模板
Route::resource('sys/template', 'sysadmin/Template');
//提交php嵌套后的模板
Route::post('sys/uploadphptemplate', "sysadmin/Template/uploadPHPTemplate");
//提交原始模板
Route::post('sys/uploadtemplate', "sysadmin/Template/uploadTemplate");
//模板缩略图
Route::post('sys/uploadthumbnails', "sysadmin/Template/uploadThumbnails");
//企业审核认证
Route::post('sys/checkPass/:id/:num', "sysadmin/Company/checkPass");


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
//图集
Route::resource('imglist', 'admin/ImgList');
//content_get
Route::resource('content_get', 'admin/ContentGet');
//添加与修改图片
Route::post('articleshowhtml', 'admin/Article/articleshowhtml');
Route::post('article/sync', 'admin/Article/syncArticle');
Route::post('article/csvupload', 'admin/Article/csvupload');
Route::post('article/csvimport', 'admin/Article/csvimport');
Route::get('article/getErrorInfo', 'admin/Article/getErrorInfo');
Route::get('article/getErrorStatus', 'admin/Article/getErrorStatus');
Route::post('article/changeErrorStatus/:id', 'admin/Article/changeErrorStatus');
Route::get('articletype/articleCount', 'admin/Articletype/ArticleCount');


//菜单
Route::resource('menu', 'admin/Menu');
Route::get('menu/getMenu', 'admin/Menu/getMenu');
Route::put('menusort/:id','admin/Menu/sort');
//获取上级菜单
Route::get('menu/upmenu/:flag/:id','admin/Menu/getUpMenu');

//问答
Route::resource('question', 'admin/Question');
//预览页面
Route::post('questionshowhtml', 'admin/Question/questionshowhtml');
//问答分类
Route::resource('questionType', 'admin/Questiontype');
Route::get('questionType/list', 'admin/Questiontype/getType');
//统计问答
Route::get('questiontype/QuestionCount', 'admin/Questiontype/QuestionCount');
//段落文章
Route::resource('scatteredArticle', 'admin/Scatteredarticle');

//段落标题 零散段落类型文章相关操作
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
//获取站点列表
Route::get('Site/getSites', 'admin/Site/getSites');
//一键生成 生成文章 清除缓存 生成栏目 生成首页
Route::get('Site/siteGetCurl/:id/:name', 'admin/Site/siteGetCurl');
//获取活动模板信息
Route::get('Site/getActivily/:id', 'admin/Site/getActivily');
//重置站点
Route::get('Site/resetSite/:id', 'admin/Site/resetSite');

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
Route::resource('admin/productType', 'admin/ProductType');
//获取产品分类列表
Route::get('admin/getProductType', 'admin/ProductType/getType');
//产品
Route::resource('admin/product', 'admin/Product');
//预览产品
Route::post('productshowhtml', 'admin/Product/productshowhtml');
//产品缩略图
Route::post('admin/uploadProductBigImg', 'admin/Product/uploadImage');

//产品 修改/添加 图片
Route::post('admin/uploadProductSerImg', 'admin/Product/uploadImgSer');
//修改添加产品多图的时候获取bug
Route::get('admin/getProductImgList/:id', 'admin/Product/getImgSer');
//删除产品图片
Route::get('admin/deleteProductImg/:id/:index', 'admin/Product/deleteImgser');


//营销策略
Route::resource('admin/Marketingmode', 'admin/Marketingmode');
//获取行业分类
Route::get('admin/getIndustry', 'admin/Industry/getIndustry');
//系统推送
Route::resource('admin/systemNotice', 'admin/SystemNotice');
//案例中心
Route::resource('admin/CaseCenter', 'admin/CaseCenter');
//修改关键词根据id和名称
Route::get('admin/updateKeyword/:id/:name', 'admin/Keyword/updateKeyword');
//事件营销活动
Route::resource('admin/eventmarketholiday', 'admin/Eventmarketingholiday');
//获取多条模板
Route::get('admin/AllHtmlTemplate/:id', 'admin/HtmlTemplate/readAll');
//html5模板模块
Route::resource('admin/HtmlTemplate', 'admin/HtmlTemplate');
//事件营销记录
Route::resource('admin/eventRecord', 'admin/EventMarketingHolidayRecord');
//追踪关键词
Route::resource('admin/trackKeyword', 'admin/TrackKeyword');
//首页统计
Route::get('admin/countDatas', 'admin/Pv/countDatas');

Route::get('admin/gettrack', 'admin/TrackKeyword/getTrack');
//获取前4条 营销图片和id
Route::get('admin/getFourMarket', 'admin/Marketingmode/getFour');
//获取软文
Route::resource('admin/softText', 'admin/SoftText');
//获取地区
Route::get('admin/getOrigin', 'admin/SoftText/getOrigin');
//获取媒体分类列表
Route::get('admin/getTypes', 'admin/SoftText/getTypes');
//根据地区获取媒体信息
Route::get('admin/returnsOrigin/:id', "admin/SoftText/returnsOrigin");
//完善企业信息
Route::resource('admin/Company', 'admin/Company');
//上传企业执照
Route::post('admin/uploadBusinessLicense', 'admin/Company/uploadBusinessLicense');
//上传法人身份证
Route::post('admin/uploadArtificialPersonId', 'admin/Company/uploadArtificialPersonId');
//上传商标
Route::post('admin/uploadTrademark', 'admin/Company/uploadTrademark');
//验证企业信息
Route::get('admin/verifyCompanyInfo', 'admin/Company/verifyCompanyInfo');
//文章图片上传打到oss
Route::post('admin/uploadarticleimage', 'admin/Article/imageupload');
//问答图片上传到oss
Route::post('admin/uploadquestionimage', 'admin/Question/imageupload');
//活动缩略图
Route::post('admin/uploadactivity', 'admin/CreativeActivity/imageUpload');
//外站活动添加
Route::post('admin/storyOut', 'admin/CreativeActivity/storyOut');
//外部活动修改
Route::post('admin/saveOut/:id', 'admin/CreativeActivity/saveOut');
//活动相关
Route::resource('admin/activityabout', 'admin/CreativeActivity');
//活动相关
Route::resource('admin/tags', 'admin/Tags');
Route::post('admin/gettags', 'admin/Tags/getTagList');
//活动 修改/添加 图片
Route::post('admin/uploadactivitySerImg', 'admin/CreativeActivity/uploadImgSer');
// 活动轮播获取
Route::get('admin/getImgSer/:id', 'admin/CreativeActivity/getImgSer');
//活动删除轮播
Route::get('admin/delImgSer/:id/:index', 'admin/CreativeActivity/deleteImgser');
//修改活动状态
Route::get('admin/changeactivityStatus/:id/:status', 'admin/CreativeActivity/changeStatus');
//易企秀帐号登录
Route::get('yiqixiu', 'admin/YiQiShow/index');

//站点logo
Route::resource('admin/siteLogo','admin/SiteLogo');
//站点logo 图片上传
Route::post('admin/sitelogoup','admin/SiteLogo/uploadLoginImg');
//站点logo列表
Route::get('admin/getsitelogolist',"admin/SiteLogo/logoList");
//站点ico
Route::resource('admin/siteIco','admin/SiteIco');
//站点ico 图片上传
Route::post('admin/siteicoup','admin/SiteIco/uploadIcoImg');
//站点ico列表
Route::get('admin/getsiteicolist',"admin/SiteIco/icoList");
//站点water_image
Route::resource('admin/siteWaterImage','admin/SiteWaterImage');
//站点water_image 图片上传
Route::post('admin/sitewaterimageup','admin/SiteWaterImage/uploadWaterImageImg');
//站点water_image 列表
Route::get('admin/getsitewaterimagelist',"admin/SiteWaterImage/waterimageList");
//搜狐新闻
Route::resource('admin/souhu',"admin/Souhu");
//搜狐分类列表
Route::get('admin/souhuList',"admin/Souhu/typeList");

//短信相关
Route::get('smssend','common/Sms/sendSms');

//支付相关
Route::get('pay/Pay','common/Pay/pay');
Route::get('pay/notify_url','common/Pay/notify_url');
Route::post('pay/return_url','common/Pay/return_url');
Route::post('pagepay','common/Pay/pagepay');

//素材库文章
Route::get('library/article', 'admin/LibraryArticle/index');
//获取一篇采集文章
Route::get('library/getOneArticle/:id', 'admin/LibraryArticle/read');
//公共图片资源路由
Route::resource('admin/libraryimgset',"admin/LibraryImgset");
Route::post('admin/uploadlibraryimage', 'admin/LibraryImgset/imageupload');
















//站点相关--------------------------------------------------------
//站点登录后的首页操作
Route::post('user/siteInfo', 'user/index/siteInfo');

//站点文章
Route::resource('user/article', 'user/Article');

Route::post('user/articleshowhtml', 'user/Article/articleshowhtml');
//文章缩略图上传到oss
Route::post('user/uploadarticleimage', 'user/Article/imageupload');

Route::get('user/articleType', 'user/Article/getArticleType');
Route::get('user/getErrorInfo', 'user/Article/getErrorInfo');
Route::post('user/changeErrorStatus/:id', 'user/Article/changeErrorStatus');
Route::get('user/getErrorStatus/', 'user/Article/getErrorStatus');

//问答
Route::resource('user/question', 'user/Question');
//问答分类
Route::get('user/QuestionType', 'user/Question/getQuestionType');

//问答预览
Route::post('user/questionshowhtml', 'user/Question/questionshowhtml');
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
Route::resource('user/articleReplaceKeyword', 'user/ArticleReplaceKeyword');
//统计所有
Route::get('user/getFour', 'user/Pv/countDatas');
//搜狐新闻
Route::resource('user/souhu',"user/Souhu");
//搜狐分类列表
Route::get('user/souhuList',"user/Souhu/typeList");
//获取站点联系方式
Route::resource('user/siteResource','user/Site');
//标签
Route::resource('user/tags', 'user/Tags');
Route::post('user/gettags', 'user/Tags/getTagList');


Route::post('common/send','common/Send/Send');
Route::get('common/site_send','common/Send/site_send');
Route::get('common/node_send','common/Send/node_send');
Route::get('common/notaddsend','common/Send/notaddsend');*/

/*
<VirtualHost *:80>
ServerAdmin jishu@qiangbi.net
#php_admin_value open_basedir "/home/wwwroot/default:/tmp/:/var/tmp/:/proc/"
DocumentRoot "/home/wwwroot/$serverpath/"
ServerName $servername
ServerAlias $serveralias
ErrorLog "/home/wwwlogs/IP-error-$servername"
CustomLog "/home/wwwlogs/IP-access_log-$servername" combined
<Directory "/home/wwwroot/$serverpath">
SetOutputFilter DEFLATE
    Options FollowSymLinks
    AllowOverride All
    Order allow,deny
    Allow from all
    DirectoryIndex index.html index.php
</Directory>
</VirtualHost>
*/

//ping -t 1 wap.163hm.com.cn         无问题
//ping -t 1 www.163hm.com.cn         无问题
//ping -t 1   m.163hmail.com.cn      已删除
//ping -t 1 www.163hmail.com.cn      无问题        163hmail/public 正常
//ping -t 1 wap.163yanxuan.cn        无问题      sitegroup-node-wap.163yanxuan.cn/public
//ping -t 1 www.163yanxuan.cn        无问题          sitegroup-node-163yanxuan.cn/public
//ping -t 1   m.jifentang.cn         无问题
//ping -t 1 www.jifentang.cn         无问题
//ping -t 1 www.cio.club             无问题
//ping -t 1   m.mall163.cn           已删除
//ping -t 1 www.mall163.cn           无问题              mall163/public
//ping -t 1 www.mall163.xyz          无问题
//ping -t 1 www.youdao.so            无问题
//
//59.111.92.173
