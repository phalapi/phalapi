<?php

/** ---------------- 根目录定义，自动加载 ---------------- **/

defined('PHALAPI_ROOT') || define('PHALAPI_ROOT', dirname(__FILE__) . '/..');

require_once PHALAPI_ROOT . '/Core/Loader.php';
//注意，这系列的接口放置在./Examples目录下
$loader = new Core_Loader(PHALAPI_ROOT, array('Examples'));
//自动加载
DI()->loader = $loader;

date_default_timezone_set('Asia/Shanghai');

Core_Translator::setLanguage('zh_cn');

/** ---------------- 注册&初始化服务组件 ---------------- **/

//配置
DI()->config = new Core_Config_File(PHALAPI_ROOT . '/Config');

//参数请求
DI()->request = new Core_Request();

//日记纪录
DI()->logger = new Core_Logger_File(PHALAPI_ROOT . '/Runtime', 
    Core_Logger::LOG_LEVEL_DEBUG | Core_Logger::LOG_LEVEL_INFO | Core_Logger::LOG_LEVEL_ERROR);

//数据操作 - 基于NotORM
DI()->notorm = function() {
    return new Core_DB_NotORM(DI()->config->get('examples.dbs'), false);
};

/** ---------------- 响应接口请求 ---------------- **/

$server = new PhalApi();
$rs = $server->response();
$rs->output();

