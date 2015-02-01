<?php
/**
 * 统一初始化
 */
 
/** ---------------- 根目录定义，自动加载 ---------------- **/

defined('API_ROOT') || define('API_ROOT', dirname(__FILE__) . '/..');

require_once API_ROOT . '/PhalApi/PhalApi.php';
$loader = new Core_Loader(API_ROOT, array('Service'));

date_default_timezone_set('Asia/Shanghai');

Core_Translator::setLanguage('zh_cn');

/** ---------------- 注册&初始化服务组件 ---------------- **/

//自动加载
DI()->loader = $loader;

//配置
DI()->config = new Core_Config_File(API_ROOT . '/Config');

//参数请求
DI()->request = new Core_Request();

//日记纪录
DI()->logger = new Core_Logger_File(API_ROOT . '/Runtime', 
    Core_Logger::LOG_LEVEL_DEBUG | Core_Logger::LOG_LEVEL_INFO | Core_Logger::LOG_LEVEL_ERROR);

//数据操作 - 基于NotORM
DI()->notorm = function() {
    return new Core_DB_NotORM(DI()->config->get('dbs'), false);
};

//缓存 - MC
Core_DI::one()->cache = function() {
	//可以考虑将此配置放进./Config/sys.php
	$mcConfig = array(
        'host' => '127.0.0.1',
        'port' => 11211,
    );
	
	$mc = new Core_Cache_Memecahced($mcConfig);
	return $mc;
};

