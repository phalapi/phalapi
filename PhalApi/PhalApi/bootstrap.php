<?php
/**
 * 出于性能考虑，手动引入必须的类文件
 * @author dogstar 20170730
 */

$baseDir = dirname(__FILE__);

require_once $baseDir . DIRECTORY_SEPARATOR . 'Api.php';
require_once $baseDir . DIRECTORY_SEPARATOR . 'ApiFactory.php';
require_once $baseDir . DIRECTORY_SEPARATOR . 'Cache.php';
require_once $baseDir . DIRECTORY_SEPARATOR . 'Config.php';
require_once $baseDir . DIRECTORY_SEPARATOR . 'DB.php';
require_once $baseDir . DIRECTORY_SEPARATOR . 'DI.php';
require_once $baseDir . DIRECTORY_SEPARATOR . 'Filter.php';
require_once $baseDir . DIRECTORY_SEPARATOR . 'Logger.php';
require_once $baseDir . DIRECTORY_SEPARATOR . 'Request.php';
require_once $baseDir . DIRECTORY_SEPARATOR . 'Response.php';
require_once $baseDir . DIRECTORY_SEPARATOR . 'Translator.php';

require_once $baseDir . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'File.php';
require_once $baseDir . DIRECTORY_SEPARATOR . 'Helper' . DIRECTORY_SEPARATOR . 'Tracer.php';
require_once $baseDir . DIRECTORY_SEPARATOR . 'Logger' . DIRECTORY_SEPARATOR . 'File.php';
require_once $baseDir . DIRECTORY_SEPARATOR . 'Request' . DIRECTORY_SEPARATOR . 'Var.php';
require_once $baseDir . DIRECTORY_SEPARATOR . 'Request' . DIRECTORY_SEPARATOR . 'Formatter.php';
require_once $baseDir . DIRECTORY_SEPARATOR . 'Request' . DIRECTORY_SEPARATOR . 'Formatter' . DIRECTORY_SEPARATOR . 'Base.php';
require_once $baseDir . DIRECTORY_SEPARATOR . 'Response' . DIRECTORY_SEPARATOR . 'Json.php';


