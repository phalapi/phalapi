<?php
/**
 * examples接口测试入口
 * @author: dogstar 2015-01-28
 */
 
/** ---------------- 根目录定义，自动加载 ---------------- **/

defined('API_ROOT') || define('API_ROOT', dirname(__FILE__) . '/../..');

require_once API_ROOT . '/PhalApi/PhalApi.php';
//注意，这系列的接口放置在./Examples目录下
$loader = new Core_Loader(API_ROOT, array('Examples'));
//自动加载
DI()->loader = $loader;

date_default_timezone_set('Asia/Shanghai');

Core_Translator::setLanguage('zh_cn');

/** ---------------- 注册&初始化服务组件 ---------------- **/

//配置
DI()->config = new Core_Config_File(API_ROOT . '/Config');

//参数请求
DI()->request = new Core_Request();

//日记纪录 - Explorer
DI()->logger = new Core_Logger_Explorer(API_ROOT . '/Runtime', 
    Core_Logger::LOG_LEVEL_DEBUG | Core_Logger::LOG_LEVEL_INFO | Core_Logger::LOG_LEVEL_ERROR);

//数据操作 - 基于NotORM - debug
DI()->notorm = function() {
    return new Core_DB_NotORM(DI()->config->get('examples.dbs'), true);
};

