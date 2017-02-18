<?php
/**
 * demo API test entrance
 * @author dogstar 2015-01-28
 */
 
/** ---------------- require init.php ---------------- **/

require_once dirname(__FILE__) . '/../../Public/init.php';

DI()->loader->addDirs('Demo');

// set logger to Explorer
DI()->logger = new PhalApi_Logger_Explorer(
	PhalApi_Logger::LOG_LEVEL_DEBUG | PhalApi_Logger::LOG_LEVEL_INFO | PhalApi_Logger::LOG_LEVEL_ERROR);

