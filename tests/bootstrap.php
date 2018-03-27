<?php
/**
 * 单元测试启动文件
 * @author dogstar 20170703
 */
 
use PhalApi\Logger;
use PhalApi\Logger\ExplorerLogger;

/** ---------------- 根目录定义，自动加载 ---------------- **/

require_once dirname(__FILE__) . '/../public/init.php';

// 兼容高版本的PHPUnit
if (!class_exists('PHPUnit_Framework_TestCase')) {
    class PHPUnit_Framework_TestCase extends \PHPUnit\Framework\TestCase { }
}

//日记纪录 - Explorer
\PhalApi\DI()->logger = new ExplorerLogger(
	Logger::LOG_LEVEL_DEBUG | Logger::LOG_LEVEL_INFO | Logger::LOG_LEVEL_ERROR);

