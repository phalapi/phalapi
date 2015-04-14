<?php 
/**
 * 以下配置为系统级的配置，通常放置不同环境下的不同配置
 */

return array(
	//此debug目前没有实际作用，各项目可根据需要定义使用
	//通常 DI()->debug = DI()->config->get('sys.debug')，但应支持：DI->debug = $_GET['debug']
	'debug' => false,

    /**
     * 加密
     */
    'crypt' => array(
        'mcrypt_iv' => '12345678',      //8位
    ),
);
