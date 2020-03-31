<?php
/**
 * 用户插件
 */
return array(
    'common_salt' => '*#&FD)#f34', // 公共盐值，设定后不可修改，否则会导致用户的密码失效
    'max_expire_time' => 2592000,    //一个月，登录token有效时间
);