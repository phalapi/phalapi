<?php
/**
 * examples配置
 */

return array(

    'dbs' => array(
        'servers' => array(
            'db_demo' => array(
                'host'      => '192.168.0.103',         //数据库域名
                'name'      => 'phalapi_test',          //数据库名字
                'user'      => 'root',                  //数据库用户名
                'password'  => '123456',	            //数据库密码
                'port'      => '3306',		            //数据库端口
            ),
        ),

        'tables' => array(
            '__default__' => array(
                'prefix' => 'tbl_',
                'key' => 'id',
                'map' => array(
                    array('db' => 'db_demo'),
                ),
            ),
        ),
    )
);
