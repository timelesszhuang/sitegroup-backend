全局规约：

- url 前边省略 **域名/index.php 
- 参数需要注明 每个参数 以及类型
- 需要注明请求的类型 get put delete put等
- 返回值 ：json 形式 
---

## **处理ajax请求完成之后相应的数据字段规范**  


### status 字符串且不可为空  

状态分为：

- success 表示请求成功 包含查询等数据为空的情况   
- failed 表示请求操作失败，需要给出提示   
- logout 表示需要重新登陆 没有获取到session信息   
- noauth 表示没有权限操作 比如调取接口错误   
    
### msg 可为空字符串 提醒的信息

### detail 相关操作的详情信息 可为空，可不设置该值

### data 可为空数组 请求返回的数据

---


#### 后台采用thinkphp5 路由　实例如下



标识|请求类型|生成路由规则|对应操作方法（默认）|说明
---|---|---|---|---
index|	GET|	blog|	index| 获取资源列表
save|	POST|	blog|	save|　创建新的资源
read|	GET|	blog/:id|	read|　读取一条资源
update|	PUT|	blog/:id|	update|　更新数据
delete|	DELETE|	blog/:id|	delete|　删除数据


# 公共接口

1. 登录模块 

功能 | url | 参数 | 请求类型|返回值
---|---|---|---|---
用户登录    |login| user_name,password,verify_code,remember{true or false}，login_type{node or site}|post|  data 数组： 如果选择记住我需要返回remember_key（用于自动登陆），login_type 用户类型,login_id 登陆的id
获取验证码  | captcha|| get |图片数据 
自动登录 |autoLogin|remember_key login_id login_type|post|data 数组： remember_key（用于自动登陆），login_type 用户类型,login_id 登陆的id|
退出登录 |logout| |get||
修改密码 |changePassword|old_password new_password|post|
获取文章分类列表 |get_article_type_list| |get| data数组：二维数组
登陆后获取站点列表 |get_site_list| |get| data:站点信息列表
登陆后设置站点信息 |set_site_info|site_id|post|




2. ×× 模块


# 大后台（节点管理后台）
功能 | url | 参数 | 请求类型|返回值
---|---|---|---|---
获取用户数据|user|page页码 rows多少条 选填查询条件 type，name|get|{“total”:"xxxx","data":"yyyy"}
获取单条数据 | user/id |id |get|{id:"xxx",.....}
添加用户 |user/id|          必填user_name,pwd,contacts,tel,type_name,type,name 其他选填|post||
修改用户|user/id|必填user_name,pwd,contacts,tel,type_name,type,name 其他选填|put||
删除用户信息 |user/id|id|delete|成功返回是否删除的信息
获取用户id、name|user/getAll|get|success|



