<?php
/**
 * db table map
 * 
 * @author: dogstar 2014-11-22
 */

return array(
    /**
     * avaiable db servers
     */
    'servers' => array(
        /**
        'db_demo' => array(
            'host'      => 'localhost',             //数据库域名
            'name'      => 'test',                  //数据库名字
            'user'      => 'root',                  //数据库用户名
            'password'  => '123456',	            //数据库密码
            'port'      => '3306',		            //数据库端口
        ),
         */
    ),

    /**
     * custom table map
     */
    'tables' => array(
        /**
        '__default__' => array(
            'prefix' => 'tbl_',
            'key' => 'id',
            'map' => array(
                array('db' => 'db_demo'),
                array('start' => 0, 'end' => 2, 'db' => 'db_demo'),
            ),
        ),
        'demo' => array(
            'prefix' => 'tbl_',
            'key' => 'id',
            'map' => array(
                array('db' => 'db_demo'),
                array('start' => 0, 'end' => 2, 'db' => 'db_demo'),
            ),
        ),
         */
    ),
);
