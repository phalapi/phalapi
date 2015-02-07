<?php
/**
 * 统一初始化
 */
 
/** ---------------- 根目录定义，自动加载 ---------------- **/

defined('API_ROOT') || define('API_ROOT', dirname(__FILE__) . '/..');

require_once API_ROOT . '/PhalApi/PhalApi.php';
$loader = new PhalApi_Loader(API_ROOT);

date_default_timezone_set('Asia/Shanghai');

PhalApi_Translator::setLanguage('zh_cn');

/** ---------------- 注册&初始化服务组件 ---------------- **/

//自动加载
DI()->loader = $loader;

//配置
DI()->config = new PhalApi_Config_File(API_ROOT . '/Config');

//日记纪录
DI()->logger = new PhalApi_Logger_File(API_ROOT . '/Runtime', 
    PhalApi_Logger::LOG_LEVEL_DEBUG | PhalApi_Logger::LOG_LEVEL_INFO | PhalApi_Logger::LOG_LEVEL_ERROR);

//数据操作 - 基于NotORM
DI()->notorm = function() {
    $debug = isset($_GET['debug']) ? true : false;
    return new PhalApi_DB_NotORM(DI()->config->get('dbs'), $debug);
};

//缓存 - MC
DI()->cache = function() {
	//可以考虑将此配置放进./Config/sys.php
	$mcConfig = array(
        'host' => '127.0.0.1',
        'port' => 11211,
    );
	
	$mc = new PhalApi_Cache_Memecahced($mcConfig);
	return $mc;
};

//签名验证服务
//DI()->filter = 'Common_SignFilter';

