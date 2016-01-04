<?php
/**
 * 分库分表的自定义数据库路由配置
 * 
 * @author: dogstar
 */

return array(
    /**
     * DB数据库服务器集群
     */
    'servers' => array(
        'DB_A' => array(
            'host'      => '192.168.0.110',           //数据库域名
            'name'      => 'phalapi_test',                  //数据库名字
            'user'      => 'root',                  //数据库用户名
            'password'  => '123456',                //数据库密码
            'port'      => '3306',                  //数据库端口
        ),
        'DB_DEMO' => array(
            'host'      => '192.168.0.110',           //数据库域名
            'name'      => 'phalapi_test',                  //数据库名字
            'user'      => 'root',                  //数据库用户名
            'password'  => '123456',                //数据库密码
            'port'      => '3306',                  //数据库端口
        ),
    ),

    /**
     * 自定义路由表
     */
    'tables' => array(
        '__default__' => array(
            'prefix' => 'tbl_',
            'key' => 'id',
            'map' => array(
                array('db' => 'DB_A'),
            ),
        ),
        'demo' => array(
            'prefix' => 'tbl_',
            'key' => 'id',
            'map' => array(
                array('db' => 'DB_A'),
                array('start' => 0, 'end' => 2, 'db' => 'DB_A'),
                array('start' => 3, 'end' => 5, 'db' => 'DB_DEMO'),
            ),
        ),
    ),
);
