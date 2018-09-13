<?php
/**
 * 分库分表的自定义数据库路由配置
 * 
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author: dogstar <chanzonghuang@gmail.com> 2015-02-09
 */

 
 /**
  * 重写db路由，使其支持动态写入多数据库
  * 假设现在除了默认数据库外还有数据库 ab_1
  */
 $TABLES  = array(
    '__default__' => array(                     //先写入默认路由
        'prefix' => '',
        'key' => 'id',
        'map' => array(
            array('db' => 'db_master')
        ),
    )
 );
 //所有的数据库
 $SERVERS = array(
    'db_master' => array(                       //服务器标记
        'type'      => 'mysql',                 //数据库类型，暂时只支持：mysql, sqlserver
        'host'      => '127.0.0.1',             //数据库域名
        'name'      => 'phalapi',               //数据库名字
        'user'      => 'root',                  //数据库用户名
        'password'  => '',	                    //数据库密码
        'port'      => 3306,                    //数据库端口
        'charset'   => 'UTF8',                  //数据库字符集
    ),
    'db_1' => array(                         //另外一个数据库
        'type'      => 'mysql',
        'host'      => '127.0.0.1',
        'name'      => 'phalapi',
        'user'      => 'root',
        'password'  => '',
        'port'      => 3306,
        'charset'   => 'UTF8',
    ),
 );

 //自定义的数据库
$customServerDatabases = array(                 //除默认数据库外的数据库的表
    'db_1' => array(                            //数据库db_1的表，需要将ab_1的表名写到这里
        'table1',
        'table2',
        'table3',
    ),
    // 'db_2' => array(   //其他数据库的表
    //     'table1',
    //     'table2',
    // )
);

//将server下自定义的表写入到路由中
foreach ($customServerDatabases as $serverName => $tables) {
    foreach ($tables as $tableName) {
        $aTable = array(
            'prefix' => '',
            'key' => 'id',
            'map' => array(
                array('db' => $serverName)
            ),
        );
        $TABLES[$tableName] = $aTable;
    }
}


return array(
    /**
     * DB数据库服务器集群
     */
    'servers' => $SERVERS,

    /**
     * 自定义的路由表
     */
    'tables' => $TABLES
);
