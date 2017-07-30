<?php
/**
 * 统一初始化
 */
 
/** ---------------- 根目录定义，自动加载 ---------------- **/

date_default_timezone_set('Asia/Shanghai');

defined('API_ROOT') || define('API_ROOT', dirname(__FILE__) . '/..');

require_once API_ROOT . '/PhalApi/PhalApi.php';
$loader = new PhalApi_Loader(API_ROOT, 'Library');

if (file_exists(API_ROOT . '/vendor/autoload.php')) {
    require_once API_ROOT . '/vendor/autoload.php';
}

/** ---------------- 注册&初始化 基本服务组件 ---------------- **/

$di = DI();

// 自动加载
$di->loader = $loader;

// 配置
$di->config = new PhalApi_Config_File(API_ROOT . '/Config');

// 调试模式，$_GET['__debug__']可自行改名
$di->debug = !empty($_GET['__debug__']) ? true : $di->config->get('sys.debug');

if ($di->debug) {
    // 启动追踪器
    $di->tracer->mark();

    error_reporting(E_ALL);
    ini_set('display_errors', 'On'); 
}

// 日记纪录
$di->logger = new PhalApi_Logger_File(API_ROOT . '/Runtime', PhalApi_Logger::LOG_LEVEL_DEBUG | PhalApi_Logger::LOG_LEVEL_INFO | PhalApi_Logger::LOG_LEVEL_ERROR);

// 数据操作 - 基于NotORM
$di->notorm = function () {
    return new PhalApi_DB_NotORM(DI()->config->get('dbs'), DI()->debug);
};

// 翻译语言包设定
SL('zh_cn');

/** ---------------- 定制注册 可选服务组件 ---------------- **/

/**
// 签名验证服务
$di->filter = 'PhalApi_Filter_SimpleMD5';
 */

/**
// 缓存 - Memcache/Memcached
$di->cache = function () {
    return new PhalApi_Cache_Memcache(DI()->config->get('sys.mc'));
};
 */

/**
// 支持JsonP的返回
if (!empty($_GET['callback'])) {
    $di->response = new PhalApi_Response_JsonP($_GET['callback']);
}
 */
