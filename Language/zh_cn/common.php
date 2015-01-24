<?php
/**
 * 翻译说明：
 *
 * 1、带大括号的保留原来写法，如：{name}，会被系统动态替换
 * 2、没在以下出现的，可以自行追加
 *
 */

return array(
    /** ---------------------- 框架核心类库的翻译 - 开发维护 ---------------------- **/
    'service ({service}) illegal' => '非法服务：{service}',
    'no such service as {className}' => '服务{className}不存在',
    'mcrypt_module_open with {cipher}' => 'mcrypt_module_open with {cipher}',
    'No table map config for {tableName}' => '缺少表{tableName}的配置',
    'Call to undefined method Core_DI::{name}() .' => '调用了未定义的方法Core_DI::{name}()',
    "miss {name}'s enum range" => '{name}缺少枚举范围',
    '{name} should be in {range}, but now {name} = {value}' => '参数{name}应该为：{range}，但现在{name} = {value}',
    "min should <= max, but now {name} min = {min} and max = {max}" => '最小值应该小于等于最大值，但现在{name}的最小值为：{min}，最大值为：{max}',
    '{name} should >= {min}, but now {name} = {value}' => '{name}应该小于或等于{min}, 但现在{name} = {value}',
    'miss name for rule' => '参数规则缺少name',
    'wrong param: {name}' => '非法参数：{name}',

    /** ---------------------- 项目应用的业务的翻译 - 产品维护 ---------------------- **/
);
