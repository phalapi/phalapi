<?php
/**
 * 统一初始化
 */
 
/** ---------------- 根目录定义，自动加载 ---------------- **/

defined('PHALAPI_ROOT') || define('PHALAPI_ROOT', dirname(__FILE__) . '/..');

date_default_timezone_set('Asia/Shanghai');

require_once PHALAPI_ROOT . '/Core/Loader.php';
$loader = new Core_Loader(PHALAPI_ROOT, array('Service'));

/** ---------------- 注册&初始化服务组件 ---------------- **/

//自动加载
Core_DI::one()->loader = $loader;

//配置
Core_DI::one()->config = new Core_Config_File(PHALAPI_ROOT . '/Config');

//参数请求
Core_DI::one()->request = new Core_Request();

//日记纪录
Core_DI::one()->logger = new Core_Logger_File(PHALAPI_ROOT . '/Runtime', 
    Core_Logger::LOG_LEVEL_DEBUG | Core_Logger::LOG_LEVEL_INFO | Core_Logger::LOG_LEVEL_ERROR);

//数据操作 - 基于NotORM
Core_DI::one()->notorm = function() {
    return new Core_DB_NotORM(Core_DI::one()->config->get('dbs'), false);
};

//缓存 - MC
Core_DI::one()->cache = function() {
	$mc = new Core_Cache_Memecahced(Core_DI::one()->config->get('sys.memcached'));
	return $mc;
};

Core_Translator::setLanguage('zh_cn');

