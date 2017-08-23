<?php

use PhalApi\Logger;
use PhalApi\Logger\ExplorerLogger;
use PhalApi\Config\FileConfig;
use PhalApi\Database\NotORMDatabase;

require_once dirname(__FILE__) . '/../vendor/autoload.php';

defined('API_ROOT') || define('API_ROOT', dirname(__FILE__));
$di = \PhalApi\DI();

$di->logger = new ExplorerLogger( 
    Logger::LOG_LEVEL_DEBUG | Logger::LOG_LEVEL_INFO | Logger::LOG_LEVEL_ERROR);

$di->config = new FileConfig(dirname(__FILE__) . '/../config');

$di->debug = true;

$dbsCfg = array(
    'servers' => array(
        'db_master' => array(                         //服务器标记
            'host'      => '127.0.0.1',             //数据库域名
            'name'      => 'phalapi',               //数据库名字
            'user'      => 'root',                  //数据库用户名
            'password'  => '123',                      //数据库密码
            'port'      => 3306,                  //数据库端口
            'charset'   => 'UTF8',                  //数据库字符集
        ),
    ),                                                                                        
    'tables' => array(
        '__default__' => array(
            'prefix' => 'tbl_',
            'key' => 'id',
            'map' => array(
                array('db' => 'db_master'),
            ),
        ),
    ),
);
$taskMqCfg = $di->config->get('dbs');
$dbsCfg['tables'] = array_merge($dbsCfg['tables'], $taskMqCfg['tables']);

$di->notorm = new NotORMDatabase($dbsCfg, true);
