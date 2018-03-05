<?php
/**
 * Created by PhpStorm.
 * User: timeless
 * Date: 17-10-20
 * Time: 上午9:07
 */

// 1 代表 sysuser 系统管理员 2 是节点  3是站点管理员

return [
    //权限
    'common/Tags/getTagList' => [1, 2, 3],
    'common/Types/getType' => [1, 2, 3],
    'common/Question/questionShowHtml' => [1, 2, 3],
    'common/Product/index' => [1, 2, 3],
    'common/LibraryImgset/index' => [1, 2, 3],
    'common/LibraryImgset/save' => [1, 2, 3],
    //'common/LibraryImgset/delete'=>[1,2,3],
    'common/LibraryImgset/update' => [1, 2, 3],
    'common/LibraryImgset/read' => [1, 2, 3],
    'common/Article/index' => [1, 2, 3],
    'common/Article/save' => [1, 2, 3],
    //'common/Article/delete' => [1,2],
    'common/Article/update' => [1, 2, 3],
    'common/Article/read' => [1, 2, 3],
    'common/TypeTag/index' => [1, 2],
    'common/TypeTag/save' => [1, 2],
    'common/TypeTag/update' => [1, 2],
    'common/TypeTag/read' => [1, 2],
    //'common/TypeTag/delete' => [1,2],
    'common/Question/index' => [1, 2, 3],
    'common/Question/save' => [1, 2, 3],
    'common/Question/update' => [1, 2, 3],
    'common/Question/read' => [1, 2, 3],
    //'common/Question/delete' => [1,2],
    'common/Types/index' => [1, 2],
    'common/Types/save' => [1, 2],
    'common/Types/update' => [1, 2],
    'common/Types/read' => [1, 2],
    //'common/Types/delete' => [1,2],
    'common/Tags/index' => [1, 2],
    'common/Tags/save' => [1, 2],
    'common/Tags/update' => [1, 2],
    'common/Tags/read' => [1, 2],
    //'common/Tags/delete' => [1,2],
    'common/Article/csvimport' => [1, 2, 3],
    'common/Article/articleShowHtml' => [1, 2, 3],
    'common/OssUpload/imageUpload' => [1, 2, 3],
    // node 节点相关权限管理
    // 产品模块  非公共模块 只有node有该功能
    'admin/Product/index' => [1, 2, 3],
    'admin/Product/save' => [1, 2, 3],
    'admin/Product/update' => [1, 2, 3],
    'admin/Product/read' => [1, 2, 3],
    //图片集相关操作
    'admin/ImgList/index' => [2],
    'admin/ImgList/read' => [2],
    'admin/ImgList/save' => [2],
    'admin/ImgList/update' => [2],
    'admin/ImgList/changeStatus' => [2],
    'admin/ImgList/getImgSer' => [2],
    'admin/ImgList/uploadImgSer' => [2],
    'admin/ImgList/deleteImgser' => [2],
    'admin/ImgList/saveInfo' => [2],
    // 产品相关接口
    'admin/Product/getImgSer' => [2],
    'admin/Product/uploadImgSer' => [2],
    'admin/Product/deleteImgser' => [2],
    'admin/Product/productShowHtml' => [2],
    'common/Qicq/index' => [2,3],
    'common/Qicq/read' => [2,3],
    'common/Souhu/index' => [2,3],
    'common/Souhu/read' => [2,3],
    'common/Wangyi/index' => [2,3],
    'common/Wangyi/read' => [2,3],
    'common/Hotnews/index' => [2,3],
    'common/Hotnews/read' => [2,3],
    'common/Souhu/getTypes' =>[2,3],
    'common/Qicq/getTypes' => [2,3],
    'common/Wangyi/getTypes' => [2,3],
    'admin/Keyword/index' => [2],
    'admin/Keyword/read' => [2],
    'admin/Keyword/update' => [2],
    'admin/Keyword/save' => [2],
    'admin/Keyword/delete' => [2],
    'admin/Keyword/deleteAll' => [2],
    'admin/Keyword/getKeywordByFile' => [2],
    'common/AccountOperation/getLanderInfo' => [1, 2, 3],
    'common/LibraryArticle/index' => [2],
    'common/LibraryArticle/read' => [2],
    'common/LibraryArticle/update' => [2],
    'common/Home/getLanderInfo' => [1, 2, 3],
    'common/Home/countDatas' => [1, 2, 3],
    'common/Marketingmode/index' => [1, 2, 3],
    'common/Marketingmode/read' => [1, 2, 3],
    'common/Marketingmode/update' => [1],
    'common/Marketingmode/save' => [1],
    'common/Marketingmode/delete' => [1],
    'common/CaseCenter/index' => [1, 2, 3],
    'common/CaseCenter/read' => [1, 2, 3],
    'common/CaseCenter/save' => [1],
    'common/CaseCenter/update' => [1],
    'common/CaseCenter/delete' => [1],
    'common/SystemNotice/index' => [1, 2],
    'common/SystemNotice/read' => [1, 2],
    'common/SystemNotice/save' => [1, 2],
    'common/SystemNotice/update' => [2, 1],
    'common/SystemNotice/delete' => [1],
    'common/Industry/getIndustry' => [1, 2, 3],
    'common/Industry/index' => [1, 2, 3],
    'common/Industry/read' => [1, 2, 3],
    'common/Industry/save' => [1, 2, 3],
    'common/Industry/update' => [1, 2, 3],
    'common/Industry/delete' => [1, 2, 3],
    'common/Home/RootCountDatas' => [2, 1],
    'common/Home/en' => [2, 1],
    'common/Home/show' => [2, 1],
    'admin/Keyword/keywordCount' => [2, 1],
    'common/Node/nodeList' => [2, 1],
    'common/Home/getMarketMode' => [2, 3],
    'common/Home/getCaseCenter' => [2, 3],
    'common/SystemNotice/readcount' => [2],
    'common/SystemNotice/nodenotice' => [2],
    'common/SystemNotice/readstatus' => [2,3],
    'common/SystemNotice/unreadnum' => [2],
    'common/SystemNotice/getErrorInfo' => [2],
    'common/SystemNotice/error_status' => [2,3],
    'common/SystemNotice/getErrorStatus' => [2],
    'common/SystemNotice/readerror'=>[2,3],
    'common/VoiceCdr/index' => [1,2],
    'common/User/index' => [1],
    'common/User/read' => [1],
    'common/User/save' => [1],
    'common/User/update' => [1],
    'common/User/delete' => [1],
    'common/User/getAll'=>[1],
    'common/Node/index' => [1],
    'common/Node/read' => [1],
    'common/Node/save' => [1],
    'common/Node/update' => [1],
    'common/Node/delete' => [1],
    'common/Company/index' => [1],
    'common/Company/read' => [1],
    'common/Company/save' => [1],
    'common/Company/update' => [1],
    'common/Company/delete' => [1],
    'common/Company/getAll' => [1],
    'common/Template/index' => [1,2],
    'common/Template/read' => [1,2],
    'common/Template/save' => [1,2],
    'common/Template/update' => [1,2],
    'common/Template/delete' => [1,2],
    'common/Template/addTemp' => [1],
    'common/Template/oldTemplate' => [2,1],
    'common/Template/uploadOldtemplate' => [2,1],
    'common/Template/uploadTemplate' => [2,1],
    'common/AccountOperation/changePassword' => [3,2,1],
    'common/Menu/index' => [2],
    'common/Menu/read' => [2],
    'common/Menu/save' => [2],
    'common/Menu/update' => [2],
    'common/Menu/getMenu' => [2],
    'common/Menu/sort' => [2],
    'common/Menu/getUpMenu' => [2],
    'common/Menutag/getTags'=>[2],
    'common/Menutag/index' => [2],
    'common/Menutag/read' => [2],
    'common/Menutag/save' => [2],
    'common/Menutag/update' => [2],
    'common/UserDefinedForm/index' => [1,2],
    'common/UserDefinedForm/read' => [1,2],
    'common/UserDefinedForm/save' => [1,2],
    'common/UserDefinedForm/update' => [1,2],
    'common/UserDefinedForm/getFormCode' => [1,2],
    'common/Links/index' => [2],
    'common/Links/read' => [2],
    'common/Links/save' => [2],
    'common/Links/update' => [2],
    'common/Links/getLinks'=>[2],
    'common/Code/index' => [2],
    'common/Code/read' => [2],
    'common/Code/save' => [2],
    'common/Code/update' => [2],
    'common/Code/delete' => [2],
    'common/Domain/index' => [2],
    'common/Domain/read' => [2],
    'common/Domain/save' => [2],
    'common/Domain/update' => [2],
    'common/Domain/delete' => [2],
    'common/Domain/getOffice'=>[2],
    'common/Contactway/index' => [2],
    'common/Contactway/read' => [2],
    'common/Contactway/save' => [2],
    'common/Contactway/update' => [2],
    'common/Contactway/delete' => [2],
    'common/Contactway/getContactway' => [2],
    'common/SiteLogo/index' => [2],
    'common/SiteLogo/read' => [2],
    'common/SiteLogo/save' => [2],
    'common/SiteLogo/update' => [2],
    'common/SiteLogo/logoList'=>[2],
    'common/SiteIco/index' => [2],
    'common/SiteIco/read' => [2],
    'common/SiteIco/save' => [2],
    'common/SiteIco/update' => [2],
    'common/SiteIco/icoList'=>[2],
    'common/SiteWaterImage/index' => [2],
    'common/SiteWaterImage/read' => [2],
    'common/SiteWaterImage/save' => [2],
    'common/SiteWaterImage/update' => [2],
    'common/SiteWaterImage/waterimageList' => [2],
    'common/Sitetype/index' => [2],
    'common/Sitetype/read' => [2],
    'common/Sitetype/save' => [2],
    'common/Sitetype/update' => [2],
    'common/Sitetype/getSiteType' => [2],
    'common/Site/getActivily' => [2],
    'common/Site/index' => [2],
    'common/Site/read' => [2],
    'common/Site/save' => [2],
    'common/Site/update' => [2],
    'common/Site/setMainSite' => [2],
    'common/Site/mobileSite'=>[2],
    'common/Site/ignoreFrontend'=>[2],
    'common/Site/getSites'=>[2],
    'common/Site/siteGetCurl'=>[2,3],
    'common/Site/resetSite'=>[2],
    'common/Site/commontype'=>[2],
    'common/Site/saveCdn'=>[2],
    'common/Template/getTemplate'=>[2],
    'common/Template/filelist'=>[2],
    'common/Template/templateRead'=>[2],
    'common/Template/readFile'=>[2],
    'common/Template/savetemplate'=>[2],
    'common/ContentGet/index' => [2],
    'common/ContentGet/read' => [2],
    'common/ContentGet/save' => [2],
    'common/ContentGet/update' => [2],
    'common/ContentGet/delete' => [2],
    'common/Staticconfig/index' => [2,3],
    'common/Staticconfig/read' => [2,3],
    'common/Staticconfig/save' => [2,3],
    'common/Staticconfig/update' => [2,3],
    'common/Staticconfig/delete' => [2,3],
    'common/Tdk/save' => [2],
    'common/Tdk/search' => [2],
    'common/Tdk/read' => [2],
    'common/Tdk/getAkeyword' => [2],
    'common/Tdk/editpageinfo' => [2],
    'common/MainkeywordKeywords/index'=>[2],
    'common/TrackKeyword/index' => [2],
    'common/TrackKeyword/read' => [2],
    'common/TrackKeyword/save' => [2],
    'common/TrackKeyword/update' => [2],
    'common/TrackKeyword/delete' => [2],
    'common/TrackKeyword/getTrack'=>[2],
    'common/MainkeywordSearch/index'=>[2],
    'common/MainkeywordSearch/mainkeyword'=>[2],
    'common/Rejection/index'=>[2],
    'common/Rejection/getUserDefind'=>[2],
    'common/CreativeActivity/imageUpload'=>[2],
    'common/CreativeActivity/storyOut'=>[2],
    'common/CreativeActivity/saveOut'=>[2],
    'common/CreativeActivity'=>[2],
    'common/CreativeActivity/uploadImgSer'=>[2],
    'common/CreativeActivity/getImgSer'=>[2],
    'common/CreativeActivity/deleteImgser'=>[2],
    'common/CreativeActivity/changeStatus'=>[2],
    'common/ArticleInsertA/index' => [3],
    'common/ArticleInsertA/read' => [3],
    'common/ArticleInsertA/save' => [3],
    'common/ArticleInsertA/update' => [3],
    'common/ArticleInsertA/delete' => [3],
    'common/ArticleReplaceKeyword/index' => [3],
    'common/ArticleReplaceKeyword/read' => [3],
    'common/ArticleReplaceKeyword/save' => [3],
    'common/ArticleReplaceKeyword/update' => [3],
    'common/ArticleReplaceKeyword/delete' => [3],
    'common/Substitution/index' => [3],
    'common/Substitution/read' => [3],
    'common/Substitution/save' => [3],
    'common/Substitution/update' => [3],
    'common/Substitution/delete' => [3],
    'common/Pv/index' => [2,3],
    'common/Pv/countkeyword' => [2,3],
    'common/Count/index' => [2,3],
    'common/Count/enginecount' => [2,3],
    'common/Count/pvStatistic'=>[2,3],
    'common/Count/ArticleCount'=>[2],
    'common/Count/QuestionCount'=>[2],
    'common/Count/searchBrowse'=>[3],

];