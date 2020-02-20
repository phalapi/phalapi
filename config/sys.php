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

    /**
     * 文件日记
     */
    'file_logger' => array(
        'log_folder' => API_ROOT . '/runtime',  // 日记目录，需要使用已存在且有写入权限的绝对目录路径
        'level' => 7,                           // 需要纪录的日记级别，默认：Logger::LOG_LEVEL_DEBUG(1) | Logger::LOG_LEVEL_INFO(2) | Logger::LOG_LEVEL_ERROR(4)
        'date_format' => 'Y-m-d H:i:s',         // 时间日期格式
        'debug' => NULL,                        // 是否调试，文件日记服务独有的调度开关，为NULL时默认跟随DI的调试模式
        'file_prefix' => '',                    // 文件名前缀，必须为有效的文件名组成部分，自动使用下划线连接系统文件
        'separator' => "|",                     // 日记内容分隔符，如：\t，注意使用双引号保持转义
    ),

    /**
     * 返回结果
     */
    'response' => array(
        'structure_map' => array( // 返回结构字段映射配置
            'ret'   => 'ret',
            'data'  => 'data',
            'msg'   => 'msg',
            'debug' => 'debug',
        ),
    ),
);
