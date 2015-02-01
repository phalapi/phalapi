<?php
/**
 * examples接口测试入口
 * @author: dogstar 2015-01-28
 */
 
/** ---------------- 根目录定义，自动加载 ---------------- **/

require_once dirname(__FILE__) . '/../../Public/init.php';

DI()->loader->addDirs('Examples');

//日记纪录 - Explorer
DI()->logger = new Core_Logger_Explorer(API_ROOT . '/Runtime', 
    Core_Logger::LOG_LEVEL_DEBUG | Core_Logger::LOG_LEVEL_INFO | Core_Logger::LOG_LEVEL_ERROR);

