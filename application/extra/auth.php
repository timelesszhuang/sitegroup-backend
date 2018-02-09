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
    'admin/Product/getImgSer'=>[2],
    'admin/Product/uploadImgSer'=>[2],
    'admin/Product/deleteImgser'=>[2],
    'admin/Product/productShowHtml'=>[2],
    'common/Qicq/index' => [2],
    'common/Qicq/read' => [2],
    'common/Souhu/index' => [2],
    'common/Souhu/read' => [2],
    'common/Wangyi/index' => [2],
    'common/Wangyi/read' => [2],
    'common/Hotnews/index' => [2],
    'common/Hotnews/read' => [2],
    'common/Souhu/getTypes'=>[2],
    'common/Qicq/getTypes'=>[2],
    'common/Wangyi/getTypes'=>[2],


];