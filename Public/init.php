<?php
/**
 * 统一初始化
 */
 
/** ---------------- 根目录定义，自动加载 ---------------- **/

date_default_timezone_set('Asia/Shanghai');

defined('API_ROOT') || define('API_ROOT', dirname(__FILE__) . '/..');

require_once API_ROOT . '/PhalApi/PhalApi.php';
$loader = new PhalApi_Loader(API_ROOT);

/** ---------------- 注册&初始化服务组件 ---------------- **/

//自动加载
DI()->loader = $loader;

//配置
DI()->config = new PhalApi_Config_File(API_ROOT . '/Config');

//日记纪录
DI()->logger = new PhalApi_Logger_File(API_ROOT . '/Runtime', 
    PhalApi_Logger::LOG_LEVEL_DEBUG | PhalApi_Logger::LOG_LEVEL_INFO | PhalApi_Logger::LOG_LEVEL_ERROR);

//数据操作 - 基于NotORM，$_GET['__sql__']可自行改名
DI()->notorm = function() {
    $debug = !empty($_GET['__sql__']) ? true : false;
    return new PhalApi_DB_NotORM(DI()->config->get('dbs'), $debug);
};

//调试模式，$_GET['__debug__']可自行改名
DI()->debug = !empty($_GET['__debug__']) ? true : DI()->config->get('sys.debug');

//翻译语言包设定
SL('zh_cn');

/** ---------------- 以下服务组件就根据需要定制注册 ---------------- **/

/**
//缓存 - Memcache/Memcached
DI()->cache = new PhalApi_Cache_Memcache(DI()->config->get('sys.mc'));
 */

/**
//签名验证服务
DI()->filter = 'PhalApi_Filter_SimpleMD5';
 */

/**
//支持JsonP的返回
if (!empty($_GET['callback'])) {
    DI()->response = new PhalApi_Response_JsonP($_GET['callback']);
}
 */
