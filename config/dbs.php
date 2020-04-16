<?php
/**
 * 分库分表的自定义数据库路由配置
 * 
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author: dogstar <chanzonghuang@gmail.com> 2015-02-09
 */

return array(
    /**
     * DB数据库服务器集群 / database cluster
     */
    'servers' => array(
        'db_master' => array(                       // 服务器标记 / database identify
            'type'      => 'mysql',                 // 数据库类型，暂时只支持：mysql, sqlserver / database type
            'host'      => '127.0.0.1',             // 数据库域名 / database host
            'name'      => 'phalapi',               // 数据库名字 / database name
            'user'      => 'root',                  // 数据库用户名 / database user
            'password'  => '',                      // 数据库密码 / database password
            'port'      => 3306,                    // 数据库端口 / database port
            'charset'   => 'UTF8',                  // 数据库字符集 / database charset
            'pdo_attr_string'   => false,           // 数据库查询结果统一使用字符串，true是，false否
            'driver_options' => array(              // PDO初始化时的连接选项配置
                // 若需要更多配置，请参考官方文档：https://www.php.net/manual/zh/pdo.constants.php
            ),
        ),
    ),

    /**
     * 自定义路由表
     */
    'tables' => array(
        // 通用路由
        '__default__' => array(                     // 固定的系统标志，不能修改！
            'prefix' => '',                         // 数据库统一表名前缀，无前缀保留空
            'key' => 'id',                          // 数据库统一表主键名，通常为id
            'keep_suffix_if_no_map' => true,        // 当分表未匹配时依然保留数字作为表后缀
            'map' => array(                         // 数据库统一默认存储路由
                array('db' => 'db_master'),         // db_master对应前面servers.db_master配置，须对应！
            ),
        ),


        // 单表路由（当某个表的配置或存储或存在分表时，可单独配置，请参考以下示例）
        /**
        'demo' => array(                            // 表名，不带表前缀，不带分表后缀
            'prefix' => '',                         // 当前的表名前缀
            'key' => 'id',                          // 当前的表主键名
            'keep_suffix_if_no_map' => true,        // 当分表未匹配时依然保留数字作为表后缀
            'map' => array(                         // 当前的分表存储路由配置
                array('db' => 'db_master'),         // 单表配置：array('db' => 服务器标记)
                array('start' => 0, 'end' => 2, 'db' => 'db_master'),     // 三张分表的配置：array('start' => 开始下标, 'end' => 结束下标, 'db' => 服务器标记)
            ),
        ),
         */
    ),
);
