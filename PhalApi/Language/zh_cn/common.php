<?php
/**
 * 翻译说明：
 * 1、带大括号的保留原来写法，如：{name}，会被系统动态替换
 * 2、没在以下出现的，可以自行追加
 */

return array(
    /** ---------------------- 框架核心类库的翻译 - 开发维护 ---------------------- **/
    'service ({service}) illegal'                                   => '非法服务：{service}',
    'no such service as {service}'                                  => '接口服务{service}不存在',
    'mcrypt_module_open with {cipher}'                              => 'mcrypt_module_open with {cipher}',
    'No table map config for {tableName}'                           => '缺少表{tableName}的配置',
    'Call to undefined method PhalApi_DI::{name}() .'               => '调用了未定义的方法PhalApi_DI::{name}()',
    "miss {name}'s enum range"                                      => '{name}缺少枚举范围',
    '{name} should be in {range}, but now {name} = {value}'         => '参数{name}应该为：{range}，但现在{name} = {value}',
    "min should <= max, but now {name} min = {min} and max = {max}" => '最小值应该小于等于最大值，但现在{name}的最小值为：{min}，最大值为：{max}',
    '{name} should >= {min}, but now {name} = {value}'              => '{name}应该大于或等于{min}, 但现在{name} = {value}',
    'miss name for rule'                                            => '参数规则缺少name',
    '{name} require, but miss'                                      => '缺少必要参数{name}',
    'PhalApi_Api::${name} undefined'                                => 'PhalApi_Api::${name} 未定义',
    'Bad Request: {message}'                                        => '非法请求：{message}',
    'Interal Server Error: {message}'                               => '服务器运行错误: {message}',
    "{name}'s enum range can not be empty"                          => '{name}枚举规则中的range不能为空',
    'no such db:{db} in servers'                                    => '在servers中缺少{db}的配置',
    'can not connect to database: {db}'                             => '数据库{db}连接失败',
    'can not connect to database: {db}, code: {code}, cause: {msg}' => '数据库{db}连接失败，异常码：{code}，错误原因：{msg}',
    'miss upload file: {file}'                                      => '缺少上传文件：{file}',
    'fail to upload file with error = {error}'                      => '上传文件失败，error = {error}',
    'DI()->filter should be instanceof PhalApi_Filter'              => 'DI()->filter未实现PhalApi_Filter接口',
    'wrong sign'                                                    => '签名错误',
    'invalid type: {type} for rule: {name}'                         => "{name}参数规则的类型({type})非法",
    'invalid callback for rule: {name}'                             => '{name}参数规则的回调函数非法',
    'Not the file type {ext}'                                       => '上传失败不是文件类型 {ext}',
    'redis config key [socket] not found' => 'redis配置键[socket]未设置',
);
