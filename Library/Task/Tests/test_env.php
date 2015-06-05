<?php
$_GET['debug'] = $_GET['__sql__'] = 1;

require_once dirname(__FILE__) . '/../../../Public/init.php';
//require_once '/home/dogstar/projects/library.phalapi.net/Public/init.php';

DI()->loader->addDirs('Demo');
DI()->loader->addDirs('Library');
DI()->loader->addDirs('./Library/Task/Task');

DI()->logger = new PhalApi_Logger_Explorer( 
    PhalApi_Logger::LOG_LEVEL_DEBUG | PhalApi_Logger::LOG_LEVEL_INFO | PhalApi_Logger::LOG_LEVEL_ERROR);

SL('en');
