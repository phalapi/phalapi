<?php
/**
 * 出于性能考虑，手动引入必须的类文件
 * @author dogstar 20170715
 */

$baseDir = dirname(__FILE__);

require_once $baseDir . DIRECTORY_SEPARATOR . 'Api.php';
require_once $baseDir . DIRECTORY_SEPARATOR . 'ApiFactory.php';
require_once $baseDir . DIRECTORY_SEPARATOR . 'Cache.php';
require_once $baseDir . DIRECTORY_SEPARATOR . 'Config.php';
require_once $baseDir . DIRECTORY_SEPARATOR . 'Database.php';
require_once $baseDir . DIRECTORY_SEPARATOR . 'DependenceInjection.php';
require_once $baseDir . DIRECTORY_SEPARATOR . 'Filter.php';
require_once $baseDir . DIRECTORY_SEPARATOR . 'Logger.php';
require_once $baseDir . DIRECTORY_SEPARATOR . 'Model.php';
require_once $baseDir . DIRECTORY_SEPARATOR . 'PhalApi.php';
require_once $baseDir . DIRECTORY_SEPARATOR . 'Request.php';
require_once $baseDir . DIRECTORY_SEPARATOR . 'Response.php';
require_once $baseDir . DIRECTORY_SEPARATOR . 'Translator.php';

require_once $baseDir . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'FileConfig.php';
require_once $baseDir . DIRECTORY_SEPARATOR . 'Database' . DIRECTORY_SEPARATOR . 'NotORMDatabase.php';
require_once $baseDir . DIRECTORY_SEPARATOR . 'Helper' . DIRECTORY_SEPARATOR . 'Tracer.php';
require_once $baseDir . DIRECTORY_SEPARATOR . 'Logger' . DIRECTORY_SEPARATOR . 'FileLogger.php';
require_once $baseDir . DIRECTORY_SEPARATOR . 'Model' . DIRECTORY_SEPARATOR . 'NotORMModel.php';
require_once $baseDir . DIRECTORY_SEPARATOR . 'Request' . DIRECTORY_SEPARATOR . 'Parser.php';
require_once $baseDir . DIRECTORY_SEPARATOR . 'Request' . DIRECTORY_SEPARATOR . 'Formatter.php';
require_once $baseDir . DIRECTORY_SEPARATOR . 'Request' . DIRECTORY_SEPARATOR . 'Formatter' . DIRECTORY_SEPARATOR . 'BaseFormatter.php';
require_once $baseDir . DIRECTORY_SEPARATOR . 'Response' . DIRECTORY_SEPARATOR . 'JsonResponse.php';

