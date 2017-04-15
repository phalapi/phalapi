<?php
/**
 * PhalApi initialization
 */
 
/** ---------------- define API project root folder, and register autoload ---------------- **/

date_default_timezone_set('Asia/Shanghai');

defined('API_ROOT') || define('API_ROOT', dirname(__FILE__) . '/..');

require_once API_ROOT . '/PhalApi/PhalApi.php';
$loader = new PhalApi_Loader(API_ROOT, 'Library');

/** ---------------- register & initialize base service components ---------------- **/

// autoload
DI()->loader = $loader;

// configuration
DI()->config = new PhalApi_Config_File(API_ROOT . '/Config');

// debug mode, rename $_GET['__debug__'] as you want
DI()->debug = !empty($_GET['__debug__']) ? true : DI()->config->get('sys.debug');

if (DI()->debug) {
    // start tracer
    DI()->tracer->mark();

    error_reporting(E_ALL);
    ini_set('display_errors', 'On'); 
}

// logs
DI()->logger = new PhalApi_Logger_File(API_ROOT . '/Runtime', PhalApi_Logger::LOG_LEVEL_DEBUG | PhalApi_Logger::LOG_LEVEL_INFO | PhalApi_Logger::LOG_LEVEL_ERROR);

// database operations based on NotORM
DI()->notorm = new PhalApi_DB_NotORM(DI()->config->get('dbs'), DI()->debug);

// setting language, default is English
SL('en');

/** ---------------- custom more optional service components ---------------- **/

/**
// signature verification servcie
DI()->filter = 'PhalApi_Filter_SimpleMD5';
 */

/**
// cache" Memcache/Memcached
DI()->cache = function () {
    return new PhalApi_Cache_Memcache(DI()->config->get('sys.mc'));
};
 */

/**
// support with JsonP reponse
if (!empty($_GET['callback'])) {
    DI()->response = new PhalApi_Response_JsonP($_GET['callback']);
}
 */
