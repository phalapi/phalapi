# phalapi数据库字典

## phalapi_curd表结构 
字段|类型|默认值|是否允许为NULL|索引|注释  
---|---|---|---|---|---  
id|int(10)||不为NULL|PRI|  
title|varchar(20)||允许NULL||  
content|text||允许NULL||  
state|tinyint(4)||允许NULL||  
post_date|datetime||允许NULL||  


## phalapi_portal_admin表结构 
字段|类型|默认值|是否允许为NULL|索引|注释  
---|---|---|---|---|---  
id|int(11)||不为NULL|PRI|  
username|varchar(20)||不为NULL|UNI|管理员账号  
password|varchar(100)||不为NULL||密码  
salt|varchar(64)||不为NULL||盐值  
role|varchar(20)|admin|不为NULL||管理员角色，admin普通管理员，super超级管理员  
state|tinyint(4)|1|不为NULL||状态，1可用0禁止  
created_at|datetime||允许NULL||创建时间  


## phalapi_portal_admin_role表结构 
字段|类型|默认值|是否允许为NULL|索引|注释  
---|---|---|---|---|---  
id|int(11)||不为NULL|PRI|  
role|varchar(20)||不为NULL||管理员角色  
role_name|varchar(255)||不为NULL||管理员角色名称  


## phalapi_portal_menu表结构 
字段|类型|默认值|是否允许为NULL|索引|注释  
---|---|---|---|---|---  
id|int(11)||不为NULL|PRI|  
title|varchar(50)||允许NULL||  
icon|varchar(50)||允许NULL||  
href|varchar(255)||允许NULL||  
target|varchar(10)|_self|允许NULL||  
sort_num|int(11)|0|允许NULL||  
parent_id|int(11)|0|允许NULL||  
assign_admin_roles|varchar(1000)||允许NULL||管理员角色分配，多个用竖线分割  
assgin_admin_usernames|text||允许NULL||分配的管理员ID，多个用竖线分割  


## phalapi_portal_menu_copy表结构 
字段|类型|默认值|是否允许为NULL|索引|注释  
---|---|---|---|---|---  
id|int(11)||不为NULL|PRI|  
title|varchar(50)||允许NULL||  
icon|varchar(50)||允许NULL||  
href|varchar(255)||允许NULL||  
target|varchar(10)|_self|允许NULL||  
sort_num|int(11)|0|允许NULL||  
parent_id|int(11)|0|允许NULL||  
assign_admin_roles|varchar(1000)||允许NULL||管理员角色分配，多个用竖线分割  
assgin_admin_usernames|text||允许NULL||分配的管理员ID，多个用竖线分割  


## phalapi_user表结构 
字段|类型|默认值|是否允许为NULL|索引|注释  
---|---|---|---|---|---  
id|int(10) unsigned||不为NULL|PRI|UID  
username|varchar(100)||不为NULL|UNI|用户名  
nickname|varchar(50)||允许NULL||昵称  
password|varchar(64)||不为NULL||密码  
salt|varchar(32)||允许NULL||随机加密因子  
reg_time|int(11)|0|允许NULL||注册时间  
avatar|varchar(500)||允许NULL||头像  
mobile|varchar(20)||允许NULL||手机号  
sex|tinyint(4)|0|允许NULL||性别，1男2女0未知  
email|varchar(50)||允许NULL||邮箱  


## phalapi_user_session表结构 
字段|类型|默认值|是否允许为NULL|索引|注释  
---|---|---|---|---|---  
id|bigint(20) unsigned||不为NULL|PRI|  
user_id|bigint(20)|0|允许NULL||用户id  
token|varchar(64)||允许NULL||登录token  
client|varchar(32)||允许NULL||客户端来源  
times|int(6)|0|允许NULL||登录次数  
login_time|int(11)|0|允许NULL||登录时间  
expires_time|int(11)|0|允许NULL||过期时间  
ext_data|text||允许NULL||json data here  


