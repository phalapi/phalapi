<?php
/**
 * 以下配置为系统级的配置，通常放置不同环境下的不同配置
 *
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author dogstar <chanzonghuang@gmail.com> 2017-07-13
 */

return array(
    /**
     * @var boolean 是否开启接口调试模式，开启后在客户端可以直接看到更多调试信息
     */
    'debug' => false,

    /**
     * @var boolean 是否开启NotORM调试模式，开启后仅针对NotORM服务开启调试模式
     */
    'notorm_debug' => true,

    /**
     * @var boolean 是否纪录SQL到日志，需要同时开启notorm_debug方可写入日志
     */
    'enable_sql_log' => true,

    /**
     * @var boolean 是否开启URI匹配，若未提供service（或s）参数且开启enable_uri_match才尝试进行URI路由匹配。例如：/App/User/Login映射到s=App.Usre.Login
     */
    'enable_uri_match' => false,

    /**
     * MC缓存服务器参考配置
     */
    'mc' => array(
        'host' => '127.0.0.1',
        'port' => 11211,
    ),

    /**
     * Redis缓存服务器参考配置
     */
    'redis' => array(
        'host' => '127.0.0.1',
        'port' => 6379,
	),
	
    /**
     * 加密
     */
    'crypt' => array(
        'mcrypt_iv' => '12345678', //8位
    ),
);
